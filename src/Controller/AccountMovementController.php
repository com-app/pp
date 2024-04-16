<?php

namespace App\Controller;

use App\Entity\Account;
use App\Entity\User;
use App\Entity\AccountMovement;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;

class AccountMovementController extends AbstractController
{
    #[Route('/account-movements', name: 'app_account_movements')]
    public function index(): Response
    {
        return $this->render('account_movement/index.html.twig', [
            'controller_name' => 'AccountMovementController',
        ]);
    }


        //Display performed payments
}
