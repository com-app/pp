<?php

namespace App\Controller\Backend;

use App\Entity\Transaction;
use App\Entity\User;
use App\Entity\Pay;
use App\Entity\Account;
use App\Entity\AccountMovement;

use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
class BackendController extends AbstractDashboardController
{
    #[Route('/backend', name: 'backend')]
    public function index(): Response
    {
       // return parent::index();
        
        // Option 1. You can make your dashboard redirect to some common page of your backend
        //
         $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
         return $this->redirect($adminUrlGenerator->setController(AccountMovementCrudController::class)->generateUrl());

        // Option 2. You can make your dashboard redirect to different pages depending on the user
        //
        // if ('jane' === $this->getUser()->getUsername()) {
        //     return $this->redirect('...');
        // }

        // Option 3. You can render some custom template to display a proper dashboard with widgets, etc.
        // (tip: it's easier if your template extends from @EasyAdmin/page/content.html.twig)
        //
        // return $this->render('some/path/my-dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('PEDRO-PAY::Backend');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToCrud('Trasactions', 'fa fa-tags', Transaction::class);
        yield MenuItem::linkToCrud('Payments', 'fa fa-file-text', Pay::class);
        yield MenuItem::linkToCrud('Accounts', 'fa fa-comment', Account::class);
        yield MenuItem::linkToCrud('account Moov', 'fa fa-user', AccountMovement::class);
        yield MenuItem::linkToCrud('Users', 'fa fa-comment', User::class);
        return [
            MenuItem::linkToDashboard('Dashboard', 'fa fa-home'),
            MenuItem::section('Operation'),
            MenuItem::linkToCrud('Trasactions', 'fa fa-tags', Transaction::class),
            MenuItem::linkToCrud('Payments', 'fa fa-file-text', Pay::class),
            MenuItem::section('Users'),
        ];
    }
        // yield MenuItem::linkToCrud('The Label', 'fas fa-list', EntityClass::class);
    
}
