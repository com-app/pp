<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Controller;

use App\Entity\User;
use App\Form\ChangePasswordType;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Http\Logout\LogoutUrlGenerator;

/**
 * Controller used to manage current user. The #[CurrentUser] attribute
 * tells Symfony to inject the currently logged user into the given argument.
 * It can only be used in controllers and it's an alternative to the
 * $this->getUser() method, which still works inside controllers.
 *
 * @author Romain Monteil <monteil.romain@gmail.com>
 */

class UserController extends AbstractController
{   
    #[Route('/user-profile-edit', name: 'user_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function edit(
        #[CurrentUser] User $user,
        Request $request,
        EntityManagerInterface $entityManager,
    ): Response {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

              $this->addFlash('success', 'user.updated_successfully');

            return $this->redirectToRoute('user_edit');
        }

        return $this->render('user/index.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/user-change-password', name: 'user_change_password', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function changePassword(
        #[CurrentUser] User $user,
        Request $request,
        EntityManagerInterface $entityManager,
        LogoutUrlGenerator $logoutUrlGenerastor,
    ): Response {
        $form = $this->createForm(ChangePasswordType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirect($logoutUrlGenerator->getLogoutPath());
        }

        return $this->render('user/change_password.html.twig', [
            'form' => $form,
        ]);
    }
        #[Route('/users', name: 'user_list', methods: ['GET', 'POST'])]
      public function getUsers(Request $request,EntityManagerInterface $em): Response {
           
        $uRepo = $em->getRepository(User::class);
        $users = $uRepo->findAll();   
        $jsonData = array();  
        $idx = 0;  

        foreach($users as $user) { 
         if ( ($user->getRoles()[0] == 'ROLE_USER') ){
           $temp = array(
                      $user->getId(),
                      $user->getFullName(),
                    );   
          $jsonData[$idx++] = $temp;  
        } 
      }
        if ($request->isXmlHttpRequest()) {  
                return new JsonResponse(['users'=>$jsonData]); 
        }
    
            return $this->render('account/index.html.twig', [ 'users' =>new JsonResponse( $jsonData)]);
    }
}
