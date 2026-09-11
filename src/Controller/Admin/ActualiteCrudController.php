<?php

namespace App\Controller\Admin;

use App\Entity\Actualite;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Vich\UploaderBundle\Form\Type\VichImageType;


class ActualiteCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Actualite::class;
    }

    public function configureFields(string $pageName): iterable
    {
         yield IdField::new('id')
            ->onlyOnIndex();
        
        yield TextField::new('titre', 'Titre');
        
        yield TextEditorField::new('description', 'Description');

        yield DateTimeField::new('date', 'Date');
        
        yield ImageField::new('image', 'Image')
            ->setBasePath('/uploads/actualites')
            ->onlyOnIndex();

        yield TextField::new('imageFile', 'Télécharger une image')
            ->setFormType(VichImageType::class)
            ->onlyOnForms();
    }

}
