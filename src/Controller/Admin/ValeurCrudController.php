<?php

namespace App\Controller\Admin;

use App\Entity\Valeur;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Vich\UploaderBundle\Form\Type\VichImageType;

class ValeurCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Valeur::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')
            ->onlyOnIndex();
        
        yield TextField::new('titre', 'Titre');
        
        yield TextEditorField::new('description', 'Description');
        
        // Afficher l'image en lecture seule sur la liste
        yield ImageField::new('image', 'Image')
            ->setBasePath('/uploads/valeurs')
            ->onlyOnIndex();

        yield TextField::new('imageFile', 'Télécharger une image')
            ->setFormType(VichImageType::class)
            ->onlyOnForms();
    }
}