<?php

namespace App\Controller\Admin;

use App\Entity\BlocAccueil;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;

class BlocAccueilCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return BlocAccueil::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnIndex(),
            TextField::new('titre', 'Titre'),
            TextEditorField::new('texte', 'Texte'),
        ];
    }
}
