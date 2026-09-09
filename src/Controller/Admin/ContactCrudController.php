<?php
namespace App\Controller\Admin;

use App\Entity\Contact;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;

class ContactCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Contact::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnIndex(),
            TextField::new('nom', 'Nom'),
            TextField::new('prenom', 'Prénom'),
            TextField::new('tel', 'Téléphone'),
            EmailField::new('mail', 'Email'),
            TextField::new('sujet', 'Sujet'),
            TextareaField::new('message', 'Message'),
        ];
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->disable(Action::NEW, Action::EDIT) // interdit création et modification
            ->add(Crud::PAGE_INDEX, Action::DETAIL); // ajoute le bouton "voir" dans l'index
    }
}
