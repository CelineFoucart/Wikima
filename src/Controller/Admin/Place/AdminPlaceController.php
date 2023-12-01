<?php

declare(strict_types=1);

namespace App\Controller\Admin\Place;

use App\Entity\Image;
use App\Entity\Place;
use App\Form\Admin\ImageType;
use App\Entity\Data\SearchData;
use App\Form\Search\AdvancedSearchType;
use App\Form\Admin\PlaceFormType;
use App\Repository\ImageRepository;
use App\Repository\PlaceRepository;
use App\Repository\PortalRepository;
use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Controller\Admin\AbstractAdminController;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\ExpressionLanguage\Expression;

#[Route('/admin/place')]
#[IsGranted(new Expression("is_granted('ROLE_ADMIN')"))]
final class AdminPlaceController extends AbstractAdminController
{
    protected string $entityName = "place";

    public function __construct(
        private ImageRepository $imageRepository,
        private PlaceRepository $placeRepository,
    ) {
    }

    #[Route('/', name: 'admin_app_place_list', methods:['GET'])]
    public function listAction(): Response
    {
        return $this->render('Admin/place/list.html.twig');
    }

    #[Route('/archive', name: 'admin_app_place_archive_index', methods:['GET'])]
    public function archiveIndexAction(): Response
    {
        return $this->render('Admin/place/archive.html.twig', [
            'places' => $this->placeRepository->findForAdminList(true),
        ]);
    }

    #[Route('/create', name: 'admin_app_place_create', methods:['GET', 'POST'])]
    public function createAction(Request $request, CategoryRepository $categoryRepository, PortalRepository $portalRepository): Response
    {
        $place = new Place();

        $categoryId = $request->query->getInt('category');
        if (0 !== $categoryId) {
            $category = $categoryRepository->find($categoryId);
            if ($category) {
                $place->addCategory($category);
            }
        }

        $portalId = $request->query->getInt('portal');
        if (0 !== $portalId) {
            $portal = $portalRepository->find($portalId);
            if ($portal) {
                $place->addPortal($portal);
            }
        }

        $placeId = $request->query->getInt('place');
        if (0 !== $placeId) {
            $placeParent = $this->placeRepository->find($placeId);

            if ($placeParent && $placeParent->getId() !== $place->getId()) {
                $place->addLocalisation($placeParent);
            }
        }

        $form = $this->createForm(PlaceFormType::class, $place);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) { 
            $this->placeRepository->save($place, true);
            $this->addFlash('success', "Le lieu " . $place . " a bien été créé.");

            return $this->redirectTo($request, $place->getId());
        }

        return $this->render('Admin/place/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/show', name: 'admin_app_place_show', methods:['GET'])]
    public function showAction(Place $place): Response
    {
        return $this->render('Admin/place/show.html.twig', [
            'place' => $place,
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_app_place_edit', methods:['GET', 'POST'])]
    public function editAction(Request $request, Place $place): Response
    {
        $form = $this->createForm(PlaceFormType::class, $place);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) { 
            $this->placeRepository->save($place, true);
            $this->addFlash('success', "Le lieu " . $place . " a bien été modifié.");

            return $this->redirectTo($request, $place->getId());
        }

        return $this->render('Admin/place/edit.html.twig', [
            'form' => $form->createView(),
            'place' => $place,
        ]);
    }

    #[Route('/{id}/delete', name: 'admin_app_place_delete', methods:['POST'])]
    public function deleteAction(Request $request, Place $place): Response
    {
        if ($this->isCsrfTokenValid('delete'.$place->getId(), $request->request->get('_token'))) {
            $this->placeRepository->remove($place, true);
            $this->addFlash('success', "Le lieu a été supprimé avec succès.");
        }

        return $this->redirectToRoute('admin_app_place_list', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/sticky', name: 'admin_app_place_sticky', methods:['POST'])]
    public function stickyAction(Request $request, Place $place): Response
    {
        if ($this->isCsrfTokenValid('sticky'.$place->getId(), $request->request->get('_token'))) {
            $sticky = !$place->getIsSticky();
            $place->setIsSticky($sticky);
            $this->placeRepository->save($place, true);
            $this->addFlash('success', "Le lieu a été modifié avec succès.");
        }

        return $this->redirectToRoute('app_place_show', ['slug' => $place->getSlug()], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/archive', name: 'admin_app_place_archive', methods:['POST'])]
    public function archiveAction(Request $request, Place $place): Response
    {
        if ($this->isCsrfTokenValid('archive'.$place->getId(), $request->request->get('_token'))) {
            $isArchived = (bool) $place->getIsArchived();
            $message = $isArchived ? "désarchivé" : "archivé";
            $place->setIsArchived(!$isArchived);
            $this->placeRepository->save($place, true);

            $this->addFlash('success', "Le lieu a été {$message} avec succès.");
        }

        return $this->redirectToRoute('admin_app_place_list', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/image', name: 'admin_app_place_image', methods:['GET', 'POST'])]
    public function imageAction(Place $place, Request $request): Response
    {
        $page = $request->query->getInt('page', 1);
        $excludes = [];
        if ($place->getIllustration()) {
            $excludes[] = $place->getIllustration()->getId();
        }

        $image = (new Image())->setPortals($place->getPortals())->setCategories($place->getCategories());
        $form = $this->createForm(ImageType::class, $image);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $this->imageRepository->add($image, true);
                $place->setIllustration($image);
                $this->placeRepository->save($place, true);

                $this->addFlash('success', "L'image a bien été ajoutée au lieu.");
                $uri = $request->server->get('REQUEST_URI');

                return $this->redirectToRoute('admin_app_place_image', ['id' => $place->getId()]);
            }
        } else if ('POST' === $request->getMethod()) {
            $status = $this->handleImage($request, $place);
            $uri = $request->server->get('REQUEST_URI');

            if ($uri && !$status) {
                return $this->redirect($uri);
            }

            return $this->redirectToRoute('admin_app_place_image', ['id' => $place->getId()]);
        }

        $searchData = (new SearchData())->setPage($page);
        $searchForm = $this->createForm(AdvancedSearchType::class, $searchData, ['allow_extra_fields' => true]);
        $searchForm->handleRequest($request);

        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
            $images = $this->imageRepository->search($searchData, $excludes, 15);
        } else {
            $images = $this->imageRepository->findPaginated($page, $excludes, 15);
        }

        return $this->render('Admin/place/image_place.html.twig', [
            'place' => $place,
            'images' => $images,
            'form' => $form->createView(),
            'searchForm' => $searchForm->createView(),
        ]);
    }

    private function handleImage(Request $request, Place $place): bool
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
                $place->setIllustration($image);
                $this->placeRepository->save($place, true);
                $this->addFlash('success', "L'image a bien été ajoutée.");
                return true;
            } else {
                $place->setIllustration(null);
                $this->placeRepository->save($place, true);
                $this->addFlash('success', "L'image a bien été enlevée.");
                return true;
            }
        }

        return false;
    }
}
