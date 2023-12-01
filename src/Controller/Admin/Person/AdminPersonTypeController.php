<?php

declare(strict_types=1);

namespace App\Controller\Admin\Person;

use App\Entity\PersonType;
use App\Form\Admin\PersonTypeFormType;
use App\Repository\PersonTypeRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Controller\Admin\AbstractAdminController;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\ExpressionLanguage\Expression;

#[Route('/admin/person/type')]
#[IsGranted(new Expression("is_granted('ROLE_ADMIN')"))]
final class AdminPersonTypeController extends AbstractAdminController
{
    protected string $entityName = "persontype";

    #[Route('/', name: 'admin_app_persontype_list', methods:['GET'])]
    public function listAction(PersonTypeRepository $personTypeRepository): Response
    {
        return $this->render('Admin/person_type/list.html.twig', [
            'personTypes' => $personTypeRepository->findAll(),
        ]);
    }

    #[Route('/create', name: 'admin_app_persontype_create', methods:['GET', 'POST'])]
    public function createAction(Request $request, PersonTypeRepository $personTypeRepository): Response
    {
        $personType = new PersonType();
        $form = $this->createForm(PersonTypeFormType::class, $personType);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) { 
            $personTypeRepository->save($personType, true);
            $this->addFlash('success', "Le type " . $personType->getTitle() . " a bien été créé.");

            return $this->redirectTo($request, $personType->getId());
        }

        return $this->render('Admin/person_type/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/show', name: 'admin_app_persontype_show', methods:['GET'])]
    public function showAction(PersonType $personType): Response
    {
        return $this->render('Admin/person_type/show.html.twig', [
            'personType' => $personType,
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_app_persontype_edit', methods:['GET', 'POST'])]
    public function editAction(Request $request, PersonType $personType, PersonTypeRepository $personTypeRepository): Response
    {
        $form = $this->createForm(PersonTypeFormType::class, $personType);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) { 
            $personTypeRepository->save($personType, true);
            $this->addFlash('success', "Le type " . $personType->getTitle() . " a bien été modifié.");

            return $this->redirectTo($request, $personType->getId());
        }

        return $this->render('Admin/person_type/edit.html.twig', [
            'form' => $form->createView(),
            'personType' => $personType,
        ]);
    }

    #[Route('/{id}/delete', name: 'admin_app_persontype_delete', methods:['POST'])]
    public function deleteAction(Request $request, PersonType $personType, PersonTypeRepository $personTypeRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$personType->getId(), $request->request->get('_token'))) {
            $personTypeRepository->remove($personType, true);
            $this->addFlash('success', "L'élément a été supprimé avec succès.");
        }

        return $this->redirectToRoute('admin_app_persontype_list', [], Response::HTTP_SEE_OTHER);
    }
}