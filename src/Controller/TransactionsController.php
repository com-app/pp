<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Transaction;
use App\Entity\User;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

use Doctrine\ORM\EntityManagerInterface;

use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class TransactionsController extends AbstractController
{
    #[Route('/transaction', name: 'app_transaction')]
    public function index(): Response
    {
        return $this->render('transaction/index.html.twig', [
            'controller_name' => 'TransactionController',
        ]);
    }

    //Get transaction that has not be solded transaction
    #[Route('/transactions', name: 'app_transactions')]
    public function loadTransactions(Request $request, EntityManagerInterface $em) { 

        $repository = $em->getRepository(Transaction::class);
        $transactions = $repository->findBy(['sold'=>0],['id'=>'DESC']);  
        $jsonData = [];  
        $idx = 0;  

        foreach($transactions as $transaction) { 
           $temp = [
                         $transaction->getBankName(),
                         $transaction->getAmount(),
                         $transaction->getDescription(),
                         $transaction->getDate(),
                         $transaction->getId() 
           ];   
          $jsonData[$idx++] = $temp;  
      }
        if ($request->isXmlHttpRequest()) {  
                return new JsonResponse(['data'=>$jsonData]); 
        }
         else { 
           return $this->render('transactions/index.html.twig', ['DATA' => new JsonResponse(['data'=>$jsonData]) ]); 
        } 
}  
    #[Route('/trans/{trans_id}', name: 'app_transaction_OneById')]
    public function trans_ById(Request $request, int $trans_id, EntityManagerInterface $em): Response
    {
        $repository = $em->getRepository(Transaction::class);
        $transaction = $repository->find($trans_id);  
        $data = [
          $transaction->getBankName(),
          $transaction->getAmount(),
          $transaction->getDescription(),
          $transaction->getDate(),
          $transaction->getId() 
    ];   

     if ($request->isXmlHttpRequest()) {  
          return new JsonResponse(['data'=>$data]); 
}
        return $this->render('transaction/index.html.twig', [
            'controller_name' => 'TransactionController',
        ]);
}
  
     //Get transaction that has been sold 

    #[Route('/my-trans-sold', name: 'app_transactions_sold')]
    #[IsGranted('IS_AUTHENTICATED')]
    public function loadSoldTrans(Request $request, EntityManagerInterface $em) { 
        //$exists = $this->getDoctrine()->getRepository(User::class)
        $repository = $em->getRepository(Transaction::class);
        $transactions = $repository->findBy(['sold'=>1],['id'=>'DESC']);   
        $jsonData = array();  
        $idx = 0;  

        foreach($transactions as $transaction) { 
           $temp = array(
                      $transaction->getBankName(),
                      $transaction->getAmount(),
                      $transaction->getDescription(),
                      $transaction->getDate(),
                      $transaction->getId() 
                    );   
          $jsonData[$idx++] = $temp;  
       
      }
        if ($request->isXmlHttpRequest()) {  
                return new JsonResponse(['data'=>$jsonData]); 
        }
         else { 
           return $this->render('transactions/index.html.twig', ['DATA' => new JsonResponse(['data'=>$jsonData]) ]); 
        } 
     }

    #[Route('/trans-sold', name: 'app_transactions_sold')]
    #[IsGranted('IS_AUTHENTICATED')]
    public function getSoldAllTrans(Request $request, EntityManagerInterface $em) { 
        //$exists = $this->getDoctrine()->getRepository(User::class)
        $repository = $em->getRepository(Transaction::class);
        $transactions = $repository->findBy(['sold'=>1],['id'=>'DESC']);   
        $jsonData = [];  
        $idx = 0;  

        foreach($transactions as $transaction) { 
           $temp = [
                      $transaction->getBankName(),
                      $transaction->getAmount(),
                      $transaction->getDescription(),
                      $transaction->getDate(),
                      $transaction->getId() 
           ];   
          $jsonData[$idx++] = $temp;  
       
      }
        if ($request->isXmlHttpRequest()) {  
                return new JsonResponse(['data'=>$jsonData]); 
        }
         else { 
           return $this->render('transactions/index.html.twig', ['DATA' => new JsonResponse(['data'=>$jsonData]) ]); 
        } 
     }              
}
