<?php

declare(strict_types=1);

namespace App\Controller\Admin\Place;

use App\Entity\Map;
use App\Repository\MapRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Controller\Admin\AbstractAdminController;
use App\Form\Admin\MapFormType;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/map')]
#[IsGranted(new Expression("is_granted('ROLE_ADMIN')"))]
class AdminMapController extends AbstractAdminController
{
    protected string $entityName = "map";

    public function __construct(
        private MapRepository $mapRepository,
    ) {
    }

    #[Route('/', name: 'admin_app_map_list', methods:['GET'])]
    public function listAction(): Response
    {
        return $this->render('Admin/map/list.html.twig', [
            'maps' => $this->mapRepository->findAll(),
        ]);
    }

    #[Route('/create', name: 'admin_app_map_create', methods:['GET', 'POST'])]
    public function createAction(Request $request): Response
    {
        $map = new Map();
        $form = $this->createForm(MapFormType::class, $map);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) { 
            $map->setCreatedAt(new \DateTimeImmutable());
            $this->mapRepository->save($map, true);
            $this->addFlash('success', "La carte " . $map . " a bien été créée.");

            return $this->redirectTo($request, $map->getId());
        }

        return $this->render('Admin/map/create.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/{id}/show', name: 'admin_app_map_show', methods:['GET'])]
    public function showAction(Map $map): Response
    {
        return $this->render('Admin/map/show.html.twig', [
            'map' => $map,
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_app_map_edit', methods:['GET', 'POST'])]
    public function editAction(Request $request, Map $map): Response
    {
        $form = $this->createForm(MapFormType::class, $map);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) { 
            $map->setUpdatedAt(new \DateTime());
            $this->mapRepository->save($map, true);
            $this->addFlash('success', "La carte " . $map . " a bien été éditée.");

            return $this->redirectTo($request, $map->getId());
        }

        return $this->render('Admin/map/edit.html.twig', [
            'map' => $map,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'admin_app_map_delete', methods:['GET', 'POST'])]
    public function deleteAction(Request $request, Map $map): Response
    {
        if ($this->isCsrfTokenValid('delete'.$map->getId(), $request->request->get('_token'))) {
            $this->mapRepository->remove($map, true);
            $this->addFlash('success', "La carte a été supprimée avec succès.");
        }

        return $this->redirectToRoute('admin_app_map_list', [], Response::HTTP_SEE_OTHER);
    }
}
