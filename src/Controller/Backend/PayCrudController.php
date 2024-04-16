<?php

namespace App\Controller\Backend;

use App\Entity\Pay;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class PayCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Pay::class;
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
