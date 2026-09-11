<?php

namespace App\Controller\Admin;

use App\Entity\LigneCommande;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;

class LigneCommandeCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return LigneCommande::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')
            ->onlyOnIndex();

        yield AssociationField::new('produit', 'Produit');

        yield IntegerField::new('quantite', 'Quantité');

        yield MoneyField::new('prixUnitaire', 'Prix unitaire')
            ->setCurrency('EUR')
            ->setStoredAsCents(false);
    }
}