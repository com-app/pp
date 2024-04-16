<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Defaults;
use Symfony\Component\HttpFoundation\JsonResponse;
use function Symfony\Component\String\u;


class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'app_dashboard')]
    public function index(EntityManagerInterface $em): Response
    {

        $default_rate_1 = $em->getRepository(Defaults::class)->findBy(['_key'=>'pay_rate_1'])[0]->getValue();

        $default_rate_2 = $em->getRepository(Defaults::class)->findBy(['_key'=>'pay_rate_2'])[0]->getValue();
        return $this->render('dashboard/index.html.twig', [
            'default_rate_1' =>$default_rate_1,
            'default_rate_2' =>$default_rate_2,
            'controller_name' => 'DashboardController',
        ]);
    }
 #[Route('/save-pay-rate', name: 'app_rate_save')]

    public function saveRate(Request $request, EntityManagerInterface $em): Response
    {

          if ($request->isXmlHttpRequest()) { 
                 $req = $request->request;
                 $rate_1    =  $req->get('rate_1');
                 $rate_2    =  $req->get('rate_2');
                 $default_rate_1 = $em->getRepository(Defaults::class)->findBy(['_key'=>'pay_rate_1'])[0];
                 $default_rate_2 = $em->getRepository(Defaults::class)->findBy(['_key'=>'pay_rate_2'])[0];
                 $default_rate_1->setValue($rate_1);
                 $default_rate_2->setValue($rate_2 );
                 $em->persist($default_rate_1);
                 $em->persist($default_rate_2);
                 $em->flush();
                 $soldResponse=['success','set new rate'];
                 return new JsonResponse($soldResponse); 
               }
     
        return $this->render('dashboard/index.html.twig', [
            'default_rate_1' =>820,
            'default_rate_2' =>1200,
        ]);
    }

}
