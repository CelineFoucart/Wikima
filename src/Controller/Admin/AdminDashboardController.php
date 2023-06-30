<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminDashboardController extends AbstractController
{
    #[Route('/admin', name: 'admin_app_dashboard')]
    public function dashboardAction(): Response
    {
        return $this->render('admin/dashboard.html.twig', [
            
        ]);
    }
}