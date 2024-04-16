<?php

namespace App\Controller;
use App\Entity\Account;
use App\Entity\AccountMovement;
use App\Entity\Pay;
use App\Entity\Transaction;
use App\Entity\Defaults;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;

use function Symfony\Component\String\u;
class PayController extends AbstractController
{    //Initial payment index dashboard for payments
    #[Route('/pay', name: 'app_payment')]
    #[IsGranted('IS_AUTHENTICATED')]
    public function index(Request $request, #[CurrentUser] User $user, EntityManagerInterface $em): Response
    {
       $this->forward('App\Controller\DataController::index',['request'  => $request,'em' => $em]);
       $user_id =  $user->getId();
       $theAccount = $em->getRepository(Account::class)->findBy(['owner'=>$user_id])[0];
       $default_rate_1 = $em->getRepository(Defaults::class)->findBy(['_key'=>'pay_rate_1'])[0]->getValue();
       $default_rate_2 = $em->getRepository(Defaults::class)->findBy(['_key'=>'pay_rate_2'])[0]->getValue();
       $user_acct_bal = $theAccount->getBalance();
        return $this->render('pay/index.html.twig', [
            'default_rate_1' =>$default_rate_1,
            'default_rate_2' =>$default_rate_2,
            'user_bal' =>$user_acct_bal,
        ]);
    }

   
       //payement request  action 
    #[Route('/send-pay', name: 'app_payment_send', methods: ['GET','POST'])]
    public function readPay(Request $request, EntityManagerInterface $em): Response
       {    
          
          if ($request->isXmlHttpRequest()) { 
                 $req = $request->request;
                 $trans_id    =  $req->get('trans_id');
                 $user_id     =  $req->get('user_id');
                 $pay_rate    =  $req->get('pay_rate');
                 $client_phone = $req->get('client_phone'); 
                 $pay_comment =  $req->get('pay_comment'); 
  
                $theUser = $em->getRepository(User::class)->find($user_id);
                $account = $em->getRepository(Account::class)->findBy(['owner'=>$user_id])[0];
                $acct_bal = $account->getBalance();
                $theTrans  = $em->getRepository(Transaction::class)->find($trans_id);
                $debit = $pay_rate*$theTrans->getAmount();
               
              if ( !$theTrans->isSold() ) {
                    if ($acct_bal >= $debit){
                    $account = $em->getRepository(Account::class)
                                  ->findBy(['owner'=>$user_id])[0];
                         // find the lastest Account Mouvement 
                       $account_moov = new AccountMovement();
                       $account_moov->setAcct($account)->setType('debit')->setAmount(-$debit);
                       $account_moov->setComment('DB:'.number_format($debit).'FCFA-/Pay/Trans/#'.$trans_id.'/DT: '.$account_moov->getDate()->format('d.m.y H:i:s')); 
        
                       $theTrans->setSold(TRUE);
                       $thePay = new Pay();
                       $thePay->setTransaction($theTrans);
                       $thePay->setRate($pay_rate)->setUser($theUser);
                       $thePay->setClientPhone($client_phone)->setComment($pay_comment);
                       $account->debit($debit);
                       $account_moov->setBalance($account->getBalance());
                       $em->persist($thePay);
                       $em->persist($account);
                       $em->persist($account_moov);
                       $em->flush();
                       $soldResponse=['success','Succesfull Paid!'.$account_moov->getComment()];
                      return new JsonResponse($soldResponse); 
                    }
                } 
              else {
                       $soldResponse=['danger','Transaction sold yet.Check it in payments::history'];
                         return new JsonResponse($soldResponse);
                  }
           } else{
           return $this->render('pay/index.html.twig', [
                       'default_rate' => 0.8,
                       'user_bal' => 000
           ]);
        }
       }
 // Display performed payments
 #[Route('/pay-log', name: 'app_pay_history')]
 #[IsGranted('IS_AUTHENTICATED')]
 public function paymentsHistory(Request $request,EntityManagerInterface $em): Response
 {
     $repository = $em->getRepository(Pay::class);
     $payments = $repository->findBy([],['id' => 'DESC']);   
     $jsonData = [];  
     $idx = 0 ;  
     foreach($payments as $pay) {
         //Covertion to CFA
        $CFA = $pay->getTransaction()->getAmount()*$pay->getRate(); 
        $temp = [
                      $pay->getTransaction()->getBankName(),
                      $pay->getTransaction()->getDescription(),
                      $pay->getTransaction()->getDate(),
                      $pay->getTransaction()->getAmount(),
                      $pay->getRate(),
                      (float) $CFA,
                      $pay->getUser()->getFullName(),
                      $pay->getPayedAt()->format('d.m.y H:i:s'),
                      $pay->getClientPhone(),
                      $pay->getComment()    
     ];   
       $jsonData[$idx++] = $temp;    
   }
     if ($request->isXmlHttpRequest()) {  
             return new JsonResponse(['data'=>$jsonData]); 
     }
      else { 
        return $this->render('pay/pay_history.html.twig', ['data' => new JsonResponse(['data'=>$jsonData]) ]); 
     } 
 }

    #[Route('/my-pay', name: 'app_my_payments_show')]
    #[IsGranted('IS_AUTHENTICATED')]
    public function myPayShow( 
        #[CurrentUser] User $user,
        Request $request,EntityManagerInterface $em): Response
    {
        $user_id = $user->getId();
        $repository = $em->getRepository(Pay::class);
        $today = (new \DateTime('now'))->format('Y-m-d');
        $from =(new \DateTime($today))->format('Y-m-d 00:00:00');
        $to =  (new \DateTime($today))->format('Y-m-d 23:59:59');
        $payments = $repository->findByUserFromTo($from,$to , $user_id);   
        
        $jsonData = [];  
        $idx = 0 ;  
        
        foreach($payments as $pay) {
            //Covertion to CFA
            $CFAAmount = $pay->getTransaction()->getAmount()*$pay->getRate(); 
           $temp = [
                         $pay->getTransaction()->getId(),
                         $pay->getUser()->getFullName(),
                         $pay->getTransaction()->getAmount(),
                         $pay->getRate(),
                         (float) $CFAAmount ,
                         $pay->getPayedAt()->format('d.m.y H:i:s'),
                         $pay->getClientPhone(),
                         $pay->getComment()    
                 ];   

          $jsonData[$idx++] = $temp;  
        
      }
        if ($request->isXmlHttpRequest()) {  
                return new JsonResponse(['data'=>$jsonData]); 
        }
         else { 
           return $this->render('pay/my_pay_show.html.twig', ['data' => new JsonResponse(['data'=>$jsonData]) ]); 
        } 
    }

    #[Route('/pay-sum', name: 'app_pay_sum')]
    #[IsGranted('ROLE_ADMIN')]
    public function paymentsSummary( Request $request,EntityManagerInterface $em): Response
    {
        
        $rep = $em->getRepository(User::class);
        $users = $rep->findBy([],['id'=>'DESC']);

        $req = $request->request;
        $date_from     =  $req->get('pay__date_from');
        $date_to    =  $req->get('pay__moov__date_to'); 
        $start_date = (new \DateTime($date_from))->format('Y-m-d');     // Default today's date
        $end_date =(new \DateTime($date_to))->format('Y-m-d 23:59:59');

        $jsonData = [];  
        $idx = 0 ; 
        // get all payments of period
        foreach($users as $user) {
            //Covertion to CFA
            $user_id = $user->getId();
            $rep = $em->getRepository(Pay::class);
            $pay_from_to = $rep->findByUserFromTo($start_date , $end_date  , $user_id);
           if ( [] != $pay_from_to){
                     $total_NGN = 0;
                     $total_CFA = 0;
                     $total_rate =0.0;
         
            foreach($pay_from_to as $pay__from_to) {

                       $tmp_amt_NGN  = $pay__from_to->getTransaction()->getAmount();
                       $tmp_rate = $pay__from_to->getRate();
                       $tmp_total = $tmp_rate*$tmp_amt_NGN;
                       $total_CFA += $tmp_total ;
                       $total_NGN +=  $tmp_amt_NGN;
                       $total_rate +=$tmp_rate;
               }
               $avg_rate =  $total_rate / sizeof($pay_from_to);
               $pay_count = sizeof($pay_from_to);
                  $temp = [
                      $user->getFullName(),
                      $pay_count,
                      (float) $total_NGN,
                      (float) $avg_rate,
                      (float) $total_CFA ,
                       $user_id  
                 ];   
              $jsonData[$idx++] = $temp;  
          }
        }
        if ($request->isXmlHttpRequest()) {  
                return new JsonResponse(['data'=>$jsonData]); 
        }
         else { 
           return $this->render('pay/pay_sum.html.twig', ['data' => new JsonResponse(['data'=>$jsonData]) ]); 
        } 
    }

    #[Route('/my-pay-sum', name: 'app_my_pay_sum')]
    #[IsGranted('IS_AUTHENTICATED')]
    public function myPaymentsSummary( Request $request,  #[CurrentUser] User $user, EntityManagerInterface $em): Response
    {

      $req = $request->request;
      $date_from     =  $req->get('pay__date_from');
      $date_to    =  $req->get('pay__moov__date_to'); 
      $start_date = (new \DateTime($date_from))->format('Y-m-d');     // Default today's date
      $end_date =(new \DateTime($date_to))->format('Y-m-d 23:59:59');
      
          //Covertion to CFA
          $user_id = $user->getId();
          $rep = $em->getRepository(Pay::class);
          $pay_from_to = $rep->findByUserFromTo($start_date , $end_date  , $user_id);
          $jsonData = [];
         if ( [] != $pay_from_to){
                   $total_NGN = 0;
                   $total_CFA = 0;
                   $total_rate =0.0;
          foreach($pay_from_to as $pay__from_to) {
                     $tmp_amt_NGN  = $pay__from_to->getTransaction()->getAmount();
                     $tmp_rate = $pay__from_to->getRate();
                     $tmp_total = $tmp_rate*$tmp_amt_NGN;
                     $total_CFA += $tmp_total ;
                     $total_NGN +=  $tmp_amt_NGN;
                     $total_rate +=$tmp_rate;
             }
             $avg_rate =  $total_rate / sizeof($pay_from_to);
             $pay_count = sizeof($pay_from_to);
             $jsonData[0] = [
                    $pay_count,
                    (float) $total_NGN,
                    (float) $avg_rate,
                    (float) $total_CFA ,
                     $user_id  
               ];    
      
      }
      if ($request->isXmlHttpRequest()) {  
              return new JsonResponse(['data'=>$jsonData]); 
      }
       else { 
         return $this->render('pay/my_pay_sum.html.twig', ['data' => new JsonResponse(['data'=>$jsonData]) ]); 
      } 
    }

     //Return one payement by 
     #[Route('/get-pay-by-trans/{trans_id}', name: 'app_payment_get')]
     public function payGet( 
         #[CurrentUser] User $user,
         Request $request, $trans_id,EntityManagerInterface $em): Response
     {

            $pay = $em->getRepository(Pay::class)->findOneByTransactionId($trans_id);
            $jsonData = []; 
             if (null !== $pay) {     
                $CFA = $pay->getTransaction()->getAmount()*$pay->getRate(); 
                $jsonData[0]  = array(
                          $pay->getUser()->getFullName(),
                          $pay->getTransaction()->getAmount(),
                          $pay->getRate(),
                          $CFA,
                          $pay->getClientPhone(),
                          $pay->getComment(), 
                          $pay->getPayedAt()->format('d.m.y H:i:s')  
                     );   
             }
         if ($request->isXmlHttpRequest()) {  
                 return new JsonResponse($jsonData); 
         }
          else { 
            return $this->render('pay/pay_show.html.twig', ['data' => new JsonResponse($jsonData)]); 
         } 
     }
    
}
