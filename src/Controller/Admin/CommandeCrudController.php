<?php

namespace App\Controller\Admin;

use App\Entity\Commande;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;

class CommandeCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Commande::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')
            ->onlyOnIndex();

        yield TextField::new('numeroCommande', 'N° commande');

        yield TextField::new('nom', 'Nom');

        yield TextField::new('prenom', 'Prénom');

        yield EmailField::new('email', 'Email');

        yield TextField::new('telephone', 'Téléphone');

        yield MoneyField::new('total', 'Total')
            ->setCurrency('EUR')
            ->setStoredAsCents(false);

        yield ChoiceField::new('statut', 'Statut')
            ->setChoices([
                'À payer' => 'A_PAYER',
                'Payée' => 'PAYEE',
                'Annulée' => 'ANNULEE',
            ])
            ->renderAsBadges([
                'A_PAYER' => 'warning',
                'PAYEE' => 'success',
                'ANNULEE' => 'danger',
            ]);

        yield DateTimeField::new('createdAt', 'Date')
            ->setFormat('dd/MM/yyyy HH:mm');

        yield Field::new('ligneCommandes', 'Produits réservés')
            ->onlyOnDetail()
            ->setTemplatePath('admin/commande/produits.html.twig');
    }
    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Action::INDEX, Action::DETAIL)
            ->disable(Action::EDIT, Action::DELETE);
    }
}