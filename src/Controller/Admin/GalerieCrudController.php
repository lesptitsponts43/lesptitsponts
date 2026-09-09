<?php

namespace App\Controller\Admin;

use App\Entity\Galerie;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Vich\UploaderBundle\Form\Type\VichImageType;

class GalerieCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Galerie::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')
            ->onlyOnIndex();
        
        yield TextField::new('titre', 'Titre');
        
        yield TextField::new('lien', 'Lien');
        
        yield ImageField::new('image', 'Image')
            ->setBasePath('/uploads/galerie')
            ->onlyOnIndex();

        yield TextField::new('imageFile', 'Télécharger une image')
            ->setFormType(VichImageType::class)
            ->onlyOnForms();
    }
}