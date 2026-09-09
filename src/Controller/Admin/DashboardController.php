<?php

namespace App\Controller\Admin;

use App\Entity\Produit;
use App\Entity\Contact;
use App\Entity\Actualite;
use App\Entity\BlocAccueil;
use App\Entity\Valeur;
use App\Entity\Galerie;
use App\Entity\Partenaire;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        return $this->redirect($adminUrlGenerator->setController(ProduitCrudController::class)->generateUrl());
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Association Les Petits Ponts')
            ->setFaviconPath('favicon.ico');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');

        // CRUD pour les entités
        yield MenuItem::linkToCrud('Produits', 'fas fa-futbol', Produit::class);
        yield MenuItem::linkToCrud('Contacts', 'fas fa-envelope', Contact::class);
        yield MenuItem::linkToCrud('Actualités', 'fas fa-newspaper', Actualite::class);
        yield MenuItem::linkToCrud("Bloc page d'accueil", 'fas fa-newspaper', BlocAccueil::class);
        yield MenuItem::linkToCrud("Valeur page d'accueil", 'fas fa-newspaper', Valeur::class);
        yield MenuItem::linkToCrud("Galerie photos", 'fas fa-newspaper', Galerie::class);
        yield MenuItem::linkToCrud("Partenaires", 'fas fa-newspaper', Partenaire::class);

        // Lien retour vers le site
        yield MenuItem::linkToRoute('Retour site', 'fas fa-arrow-left', 'home');
    }
}
