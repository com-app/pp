<?php

namespace App\Controller\Backend;

use App\Entity\AccountMovement;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class AccountMovementCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return AccountMovement::class;
    }

    /*
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('title'),
            TextEditorField::new('description'),
        ];
    }
    */
}
