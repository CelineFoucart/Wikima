<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\About;
use App\Form\AboutType;
use App\Repository\AboutRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_SUPER_ADMIN')")]
class AdminOverviewController extends AbstractController
{
    #[Route('/admin/about', name: 'admin_app_overview')]
    public function overviewAction(Request $request, AboutRepository $aboutRepository): Response
    {
        $overview = $aboutRepository->findAboutRow('overview');
        if (null === $overview) {
            $overview = (new About())->setType('overview');
        }

        $form = $this->createForm(AboutType::class, $overview);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $aboutRepository->add($overview);
            $this->addFlash('success', 'Les modifications ont été enregistrées.');

            return $this->redirectToRoute('admin_app_overview');
        }

        return $this->renderForm('Admin/overview.html.twig', [
            'form' => $form,
        ]);
    }
}
