<?php

declare(strict_types=1);

namespace App\Controller\Admin\Place;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Controller\Admin\AbstractAdminController;
use App\Entity\MapPosition;
use App\Form\Admin\MapPositionType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/map-position')]
#[IsGranted(new Expression("is_granted('ROLE_ADMIN')"))]
class AdminMapPositionController extends AbstractAdminController
{
    #[Route('/{id}/edit', name: 'admin_app_map_position_edit', methods:['GET', 'POST'])]
    public function editAction(Request $request, MapPosition $position, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(MapPositionType::class, $position);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) { 
            $entityManager->persist($position);
            $entityManager->flush();
            $this->addFlash('success', "L'emplacement " . $position . " a bien été édité.");

            return $this->redirectToRoute('admin_app_map_show', ['id' => $position->getMap()->getId()]);
        }

        return $this->render('Admin/map/position_edit.html.twig', [
            'map' => $position->getMap(),
            'form' => $form,
            'general_active' => true,
        ]);
    }

    #[Route('/{id}/delete', name: 'admin_app_map_position_delete', methods:['POST'])]
    public function deleteAction(Request $request, MapPosition $position, EntityManagerInterface $entityManager): Response
    {
        $map = $position->getMap();

        if ($this->isCsrfTokenValid('delete'.$position->getId(), $request->request->get('_token'))) {
            $entityManager->remove($position);
            $entityManager->flush();
            $this->addFlash('success', "L'emplacement a été supprimé avec succès.");
        }

        return $this->redirectToRoute('admin_app_map_show', ['id' => $map->getId()]);
    }
}