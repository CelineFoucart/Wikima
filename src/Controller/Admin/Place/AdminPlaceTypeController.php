<?php

declare(strict_types=1);

namespace App\Controller\Admin\Place;

use App\Entity\PlaceType;
use App\Form\Admin\PlaceTypeFormType;
use App\Repository\PlaceTypeRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Controller\Admin\AbstractAdminController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

#[Route('/admin/place/type')]
#[Security("is_granted('ROLE_ADMIN')")]
final class AdminPlaceTypeController extends AbstractAdminController
{
    protected string $entityName = "placetype";

    #[Route('/', name: 'admin_app_placetype_list', methods:['GET'])]
    public function listAction(PlaceTypeRepository $placeTypeRepository): Response
    {
        return $this->render('Admin/place_type/list.html.twig', [
            'placeTypes' => $placeTypeRepository->findAll(),
        ]);
    }

    #[Route('/create', name: 'admin_app_placetype_create', methods:['GET', 'POST'])]
    public function createAction(Request $request, PlaceTypeRepository $placeTypeRepository): Response
    {
        $placeType = new PlaceType();
        $form = $this->createForm(PlaceTypeFormType::class, $placeType);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) { 
            $placeTypeRepository->save($placeType, true);
            $this->addFlash('success', "Le type " . $placeType->getTitle() . " a bien été créé.");

            return $this->redirectTo($request, $placeType->getId());
        }

        return $this->render('Admin/place_type/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/show', name: 'admin_app_placetype_show', methods:['GET'])]
    public function showAction(PlaceType $placeType): Response
    {
        return $this->render('Admin/place_type/show.html.twig', [
            'placeType' => $placeType,
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_app_placetype_edit', methods:['GET', 'POST'])]
    public function editAction(Request $request, PlaceType $placeType, PlaceTypeRepository $placeTypeRepository): Response
    {
        $form = $this->createForm(PlaceTypeFormType::class, $placeType);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) { 
            $placeTypeRepository->save($placeType, true);
            $this->addFlash('success', "Le type " . $placeType->getTitle() . " a bien été modifié.");

            return $this->redirectTo($request, $placeType->getId());
        }

        return $this->render('Admin/place_type/edit.html.twig', [
            'form' => $form->createView(),
            'placeType' => $placeType,
        ]);
    }

    #[Route('/{id}/delete', name: 'admin_app_placetype_delete', methods:['POST'])]
    public function deleteAction(Request $request, PlaceType $placeType, PlaceTypeRepository $placeTypeRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$placeType->getId(), $request->request->get('_token'))) {
            $placeTypeRepository->remove($placeType, true);
            $this->addFlash('success', "L'élément a été supprimé avec succès.");
        }

        return $this->redirectToRoute('admin_app_placetype_list', [], Response::HTTP_SEE_OTHER);
    }
}