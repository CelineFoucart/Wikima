<?php

declare(strict_types=1);

namespace App\Controller\Admin\Person;

use App\Entity\Image;
use App\Entity\Person;
use App\Form\Admin\ImageType;
use App\Entity\Data\SearchData;
use App\Form\AdvancedSearchType;
use App\Form\Admin\PersonFormType;
use App\Repository\ImageRepository;
use App\Repository\PersonRepository;
use App\Repository\PortalRepository;
use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Controller\Admin\AbstractAdminController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

#[Route('/admin/person')]
#[Security("is_granted('ROLE_ADMIN')")]
final class AdminPersonController extends AbstractAdminController
{
    protected string $entityName = "person";

    public function __construct(
        private ImageRepository $imageRepository,
        private PersonRepository $personRepository,
    ) {
    }

    #[Route('/', name: 'admin_app_person_list', methods:['GET'])]
    public function listAction(): Response
    {
        return $this->render('Admin/person/list.html.twig');
    }

    #[Route('/archive', name: 'admin_app_person_archive_index', methods:['GET'])]
    public function archiveIndexAction(): Response
    {
        return $this->render('Admin/person/archive.html.twig', [
            'persons' => $this->personRepository->findForAdminList(true),
        ]);
    }

    #[Route('/create', name: 'admin_app_person_create', methods:['GET', 'POST'])]
    public function createAction(Request $request, CategoryRepository $categoryRepository, PortalRepository $portalRepository): Response
    {
        $person = new Person();

        $categoryId = $request->query->getInt('category');
        if (0 !== $categoryId) {
            $category = $categoryRepository->find($categoryId);
            if ($category) {
                $person->addCategory($category);
            }
        }

        $portalId = $request->query->getInt('portal');
        if (0 !== $portalId) {
            $portal = $portalRepository->find($portalId);
            if ($portal) {
                $person->addPortal($portal);
            }
        }

        $form = $this->createForm(PersonFormType::class, $person);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) { 
            $this->personRepository->add($person, true);
            $this->addFlash('success', "Le personnage " . $person . " a bien été créé.");

            return $this->redirectTo($request, $person->getId());
        }

        return $this->render('Admin/person/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/show', name: 'admin_app_person_show', methods:['GET'])]
    public function showAction(Person $person): Response
    {
        return $this->render('Admin/person/show.html.twig', [
            'person' => $person,
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_app_person_edit', methods:['GET', 'POST'])]
    public function editAction(Request $request, Person $person): Response
    {
        $form = $this->createForm(PersonFormType::class, $person);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) { 
            $this->personRepository->add($person, true);
            $this->addFlash('success', "Le personnage " . $person . " a bien été modifié.");

            return $this->redirectTo($request, $person->getId());
        }

        return $this->render('Admin/person/edit.html.twig', [
            'form' => $form->createView(),
            'person' => $person,
        ]);
    }

    #[Route('/{id}/archive', name: 'admin_app_person_archive', methods:['POST'])]
    public function archiveAction(Request $request, Person $person): Response
    {
        if ($this->isCsrfTokenValid('archive'.$person->getId(), $request->request->get('_token'))) {
            $isArchived = (bool) $person->getIsArchived();
            $message = $isArchived ? "désarchivé" : "archivé";
            $person->setIsArchived(!$isArchived);
            $this->personRepository->add($person, true);

            $this->addFlash('success', "Le personage a été {$message} avec succès.");
        }

        return $this->redirectToRoute('admin_app_person_list', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/delete', name: 'admin_app_person_delete', methods:['POST'])]
    public function deleteAction(Request $request, Person $person): Response
    {
        if ($this->isCsrfTokenValid('delete'.$person->getId(), $request->request->get('_token'))) {
            $this->personRepository->remove($person, true);
            $this->addFlash('success', "Le personnage a été supprimé avec succès.");
        }

        return $this->redirectToRoute('admin_app_person_list', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/image', name: 'admin_app_person_image', methods:['GET', 'POST'])]
    public function imageAction(Person $person, Request $request): Response
    {
        $page = $request->query->getInt('page', 1);
        $excludes = [];
        if ($person->getImage()) {
            $excludes[] = $person->getImage()->getId();
        }

        $image = (new Image())->setPortals($person->getPortals())->setCategories($person->getCategories());
        $form = $this->createForm(ImageType::class, $image);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $this->imageRepository->add($image, true);
                $person->setImage($image);
                $this->personRepository->add($person, true);

                $this->addFlash('success', "L'image a bien été ajoutée au personnage.");
                $uri = $request->server->get('REQUEST_URI');

                return $this->redirectToRoute('admin_app_person_image', ['id' => $person->getId()]);
            }
        } elseif ('POST' === $request->getMethod()) {
            $status = $this->handleImage($request, $person);
            $uri = $request->server->get('REQUEST_URI');

            if ($uri && !$status) {
                return $this->redirect($uri);
            }

            return $this->redirectToRoute('admin_app_person_image', ['id' => $person->getId()]);
        }

        $searchData = (new SearchData())->setPage($page);
        $searchForm = $this->createForm(AdvancedSearchType::class, $searchData, ['allow_extra_fields' => true]);
        $searchForm->handleRequest($request);

        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
            $images = $this->imageRepository->search($searchData, $excludes, 15);
        } else {
            $images = $this->imageRepository->findPaginated($page, $excludes, 15);
        }

        return $this->render('Admin/person/image_person.html.twig', [
            'person' => $person,
            'images' => $images,
            'form' => $form->createView(),
            'searchForm' => $searchForm->createView(),
        ]);
    }

    private function handleImage(Request $request, Person $person): bool
    {
        $imageId = $request->request->getInt('imageId');
        $image = $this->imageRepository->find($imageId);

        if (null === $image) {
            $this->addFlash('error', "L'image que vous avez choisi n'existe pas.");
            return false;
        }

        $delete = $request->request->get('delete');
        if ($this->isCsrfTokenValid('image'.$image->getId(), $request->request->get('_token'))) {
            if (null === $delete) {
                $person->setImage($image);
                $this->personRepository->add($person, true);
                $this->addFlash('success', "L'image a bien été ajoutée.");

                return true;
            } else {
                $person->setImage(null);
                $this->personRepository->add($person, true);
                $this->addFlash('success', "L'image a bien été enlevée.");
                
                return true;
            }
        }

        return false;
    }
}
