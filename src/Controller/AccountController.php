<?php

namespace App\Controller;


use App\Entity\User;
use App\Entity\Account;
use App\Entity\AccountMovement;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Doctrine\ORM\EntityManagerInterface;

use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AccountController extends AbstractController
{
    #[IsGranted('ROLE_ADMIN')]
    #[Route('/accounting', name: 'app_accounting')]
    public function index(Request $request, EntityManagerInterface $em): Response
    {
        $repository = $em->getRepository(Account::class);
        $accounts = $repository->findBy([],['id'=>'DESC']); 

        $req = $request->request;
        $acct_id    =  $req->get('acct_id');
        $date_from     =  $req->get('acct_moov__date_from');
        $date_to    =  $req->get('acct_moov__date_to'); 
        $start_date = (new \DateTime($date_from))->format('Y-m-d');     // Default today's date
        $end_date =(new \DateTime($date_to))->format('Y-m-d 23:59:59');  // Default today's date
        $jsonData =[];  
        $idx = 0 ;  

        foreach($accounts as $account) {

                 $acct_id  = $account->getId();
                 $acct_bal = $account->getBalance();

            $acct_total_credit = $em->getRepository(AccountMovement::class)->
                                 findSumByMoovTypeFromTo('credit',  $start_date, $end_date, $acct_id);

            $acct_total_debit  = $em->getRepository(AccountMovement::class)->
                                 findSumByMoovTypeFromTo('debit', $start_date, $end_date, $acct_id);
        
            $last_acct_moov =  $em->getRepository(AccountMovement::class)->findLastByFromTo($start_date, $end_date, $acct_id);

             // Creating initial sold if needed 
             if (  ($acct_bal != 0)&&($acct_total_credit  == null) && ($last_acct_moov == null )){
                    $account_moov = new AccountMovement();
                    $account->credit(0);
                    $account_moov->setAcct($account)->setType('credit')->setAmount($acct_bal);
                    $account_moov->setComment(' CR:'.number_format($acct_bal).'FCFA-/AUTO/#./DT: '.$account_moov->
                    getDate()->format('d.m.y H:i:s')); 
                    $account_moov->setBalance($acct_bal);
                    $em->persist($account);
                    $em->persist($account_moov);
                    $em->flush();
            }   

            $acct_total_credit =  ( $acct_total_credit  == null)?$acct_bal:$acct_total_credit;
            $acct_total_debit  =  ( $acct_total_debit  == null)?0:$acct_total_debit;

            $acct_moov = $acct_total_credit + $acct_total_debit; //  debit is signed
            
             $moov_type = 'Credit';
                 if ( $acct_moov == 0 || 0 == $acct_total_debit ){
                        $moov_type = 'Balance';
                   }
               // $moov_type =  $acct_bal < abs($acct_moov) ? 'Debit':'Credit';
           $temp = [
                    '00'.$acct_id.$account->getOwner()->getId(),
                     $account->getOwner()->getFullName(),
                     $acct_total_credit,
                     $acct_total_debit,
                     $acct_bal,
                     $moov_type,  
                     $acct_id   
                 ];   

          $jsonData[$idx++] = $temp;  
        
      }
        if ($request->isXmlHttpRequest()) {  
                return new JsonResponse(['data'=>$jsonData]); 
        }
         else { 
           return $this->render('account/index.html.twig', ['accounts' => $jsonData[0] ]); 
        } 
    }

    #[Route('/my-acct', name: 'app_my_accounting')]
    #[IsGranted('IS_AUTHENTICATED')]
    public function myAcount(#[CurrentUser] User $user,Request $request, EntityManagerInterface $em): Response
    {   

        $user_id = $user->getId();
        $account = $em->getRepository(Account::class)->findBy(['owner'=>$user_id])[0];
        $acct_id = $account->getId();    
        $acct_bal = $account->getBalance();
 
        $req = $request->request;
        $acct_id    =  $req->get('acct_id');
        $date_from     =  $req->get('acct_moov__date_from');
        $date_to    =  $req->get('acct_moov__date_to'); 
        $start_date = (new \DateTime($date_from))->format('Y-m-d'); // Default today's date
        $end_date =(new \DateTime($date_to))->format('Y-m-d 23:59:59');// Default today's date
        $acct_id  = $account->getId();
        $acct_bal = $account->getBalance();

            $acct_total_credit = $em->getRepository(AccountMovement::class)->
                                 findSumByMoovTypeFromTo('credit',  $start_date, $end_date, $acct_id);

            $acct_total_debit  = $em->getRepository(AccountMovement::class)->
                                  findSumByMoovTypeFromTo('debit', $start_date, $end_date, $acct_id);
            
            $last_acct_moov =  $em->getRepository(AccountMovement::class)->
                                  findLastByFromTo($start_date, $end_date, $acct_id);

              // Creating initial sold if needed 
              if (  ($acct_bal != 0)&&($acct_total_credit  == null) && ($last_acct_moov == null )){
                $account_moov = new AccountMovement();
                $account->credit(0);
                $account_moov->setAcct($account)->setType('credit')->setAmount($acct_bal);
                $account_moov->setComment(' CR:'.number_format($acct_bal).'FCFA-/AUTO/#./DT: '.$account_moov->
                getDate()->format('d.m.y H:i:s')); 
                $account_moov->setBalance($acct_bal);
                $em->persist($account);
                $em->persist($account_moov);
                $em->flush();
        }   

        $acct_total_credit =  ( $acct_total_credit  == null)?$acct_bal:$acct_total_credit;
        $acct_total_debit  =  ( $acct_total_debit  == null)?0:$acct_total_debit;
        $acct_moov = $acct_total_credit + $acct_total_debit; //  debit is signed


            $acct_moov = $acct_total_credit + $acct_total_debit; //  debit is signed
            
            $moov_type = 'Credit';
                 if ( $acct_moov == 0 || 0 == $acct_total_debit ){
                        $moov_type = 'Balance';
                   }
               $date_int = (new \DateTime($date_from))->format('d.m.y H:i:s').'-'.(new \DateTime($date_to))->format('Y-m-d 23:59:59');
               $jsonData[0] = [
                     $date_int, 
                     $account->getOwner()->getFullName(),
                     $acct_total_credit,
                     $acct_total_debit,
                     $acct_bal,
                     $moov_type,  
                     $acct_id   
                 ];     
    
        if ($request->isXmlHttpRequest()) {  
                return new JsonResponse(['data'=>$jsonData]); 
        }
         else { 
           return $this->render('account/my_acct.html.twig', ['acct_id' => $acct_id, 'date_interval' =>$date_int]); 
        } 
    }

    #[Route('/send-acct-moov', name: 'app_account_moov', methods: ['GET','POST'])]
    public function moovAccount(Request $request, EntityManagerInterface $em): Response{   
       
        if ($request->isXmlHttpRequest()) { 
                 $req = $request->request;
                 $acct_id    =  $req->get('acct_id');
                 $amount     =  $req->get('acct_moov_amount');
                 $moov_type   =  $req->get('acct_moov_type');
                 $comment   =  $req->get('acct_moov_comment');
                 $account = $em->getRepository(Account::class)->find($acct_id); 
                 $acct_bal = $account->getBalance();
                 $account_moov = new AccountMovement();

       switch ( $moov_type ){
                  case  'DB':
                   if ($acct_bal >= $amount){ // control done from UI
                         $account->debit($amount); 
                         $account_moov->setAcct($account)->setType('debit')->setAmount(-$amount);
                         $account_moov->setComment($comment.' DB:'.number_format(-$amount).'FCFA-/MST/#./DT: '.$account_moov->getDate()
                         ->format('d.m.y H:i:s')); 
                      }
                      break;
                   case 'CR':
                       $account->credit($amount);
                       $account_moov->setAcct($account)->setType('credit')->setAmount($amount);
                       $account_moov->setComment($comment.' CR:'.number_format($amount).'FCFA-/MST/#./DT: '.$account_moov->getDate()
                       ->format('d.m.y H:i:s')); 
                       break;
                       default:
                       break;
              }
                    $acct_bal = $account->getBalance(); // Updated Account Bal
                    $account_moov->setBalance($acct_bal);
                    $em->persist($account);
                    $em->persist($account_moov);
                    $em->flush();
                    $moovResponse=['success','Succesfully! Account#00'.$acct_id.' '.$moov_type.', New Bal: '.number_format($acct_bal).'FCFA'];
                      return new JsonResponse($moovResponse); 
               } else {
              return $this->render('pay/index.html.twig', [ 'default_rate' => 0.8,'user_bal' => 80000]);
           }
}
//add new account moov data
#[Route('/acct-mv', name: 'app_acct_moov', methods: ['POST'])]
public function accountMoov(Request $request, EntityManagerInterface $em): Response
{   
        
        $req = $request->request;
        $acct_id    =  $req->get('acct_id');
        $moov_type   =  $req->get('acct_moov__type');
        $date_from     =  $req->get('acct_moov__date_from');
        $date_to    =  $req->get('acct_moov__date_to');

        $start_date = (new \DateTime($date_from))->format('Y-m-d');
        $end_date =(new \DateTime($date_to))->format('Y-m-d 23:59:59');
     switch ( $moov_type ){
         case  'DB':
            $acct_moovs = $em->getRepository(AccountMovement::class)
            ->findByMoovTypeFromTo('debit',$start_date,$end_date,$acct_id);
            break;
          case 'CR':
            $acct_moovs = $em->getRepository(AccountMovement::class)
            ->findByMoovTypeFromTo('credit',$start_date,$end_date,$acct_id);
            break;
            default:
            $acct_moovs = $em->getRepository(AccountMovement::class)
            ->findByFromTo($start_date,$end_date,$acct_id);
            break;

      }  
      $idx = 0;
      $jsonData = [];
      foreach($acct_moovs as $acct_moov) {
          $temp = [
              $acct_moov->getId(),
              $acct_moov->getType(),
              $acct_moov->getAmount(),
              $acct_moov->getDate()->format('d.m.y H:i:s'),
              $acct_moov->getComment(),    
         ];  
           $jsonData[$idx++] = $temp;    
      }
          
    if ($request->isXmlHttpRequest()) {  
         return  new JsonResponse($jsonData); 
}else {
    return $this->render('account/manage_my_acct.html.twig',
         ['data' => new JsonResponse($jsonData), 'acct'=>$acct_id]);
}   
}
    // Returns the account balance of the user
    #[Route('/acct-bal/{user_id}', name: 'app_user_account_bal')]
    public function AccountBal(Request $request, EntityManagerInterface $em, int $user_id): Response
    {
        
        $repository = $em->getRepository(Account::class);
        $balance = $repository->findBy(['owner'=>$user_id])[0]->getBalance();   
        if ($request->isXmlHttpRequest()) {  
                return new JsonResponse($balance); 
        }
         else { 
           return $this->render('account/index.html.twig', ['data' => new JsonResponse($balance)]); 
        } 
    }
   
}
