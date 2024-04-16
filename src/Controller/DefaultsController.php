<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultsController extends AbstractController
{
    #[Route('/defaults', name: 'app_defaults')]
    public function index(): Response
    {
        return $this->render('defaults/index.html.twig', [
            'controller_name' => 'DefaultsController',
        ]);
    }
}
