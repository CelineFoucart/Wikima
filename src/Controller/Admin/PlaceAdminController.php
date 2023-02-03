<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Image;
use App\Entity\Place;
use App\Form\ImageType;
use App\Entity\Data\SearchData;
use App\Form\AdvancedSearchType;
use App\Repository\ImageRepository;
use App\Repository\PlaceRepository;
use App\Repository\PortalRepository;
use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class PlaceAdminController extends CRUDController
{
    public function __construct(
        private PlaceRepository $placeRepository,
        private ImageRepository $imageRepository,
        private CategoryRepository $categoryRepository,
        private PortalRepository $portalRepository
    ) {
    }

    #[Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_SUPER_ADMIN')")]
    public function imageAction(?int $id, Request $request): Response
    {
        $place = $this->placeRepository->find($id);

        if (!$place instanceof place) {
            throw new NotFoundHttpException(sprintf('unable to find the object with id: %s', $id));
        }

        $page = $request->query->getInt('page', 1);
        $excludes = [];
        if ($place->getIllustration()) {
            $excludes[] = $place->getIllustration()->getId();
        }

        $image = (new Image())->setPortals($place->getPortals())->setCategories($place->getCategories());
        $form = $this->createForm(ImageType::class, $image);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->imageRepository->add($image, true);
            $place->setIllustration($image);
            $this->placeRepository->add($place, true);

            $this->addFlash('success', "L'image a bien été ajoutée au lieu.");
            $uri = $request->server->get('REQUEST_URI');

            return $this->redirectToRoute('admin_app_place_image', ['id' => $place->getId()]);
        }
        if ('POST' === $request->getMethod()) {
            $status = $this->handleImage($request, $place);
            $uri = $request->server->get('REQUEST_URI');

            if ($uri && !$status) {
                return $this->redirect($uri);
            }

            return $this->redirectToRoute('admin_app_place_image', ['id' => $place->getId()]);
        }

        $searchData = (new SearchData())->setPage($page);
        $searchForm = $this->createForm(AdvancedSearchType::class, $searchData);
        $searchForm->handleRequest($request);

        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
            $images = $this->imageRepository->search($searchData, $excludes, 15);
        } else {
            $images = $this->imageRepository->findPaginated($page, $excludes, 15);
        }

        return $this->render('Admin/image_place.html.twig', [
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

    protected function preCreate(Request $request, object $object): ?Response
    {
        $categoryId = $request->query->getInt('category');
        if (0 !== $categoryId) {
            $category = $this->categoryRepository->find($categoryId);
            if ($category) {
                $object->addCategory($category);
            }
        }

        $portalId = $request->query->getInt('portal');
        if (0 !== $portalId) {
            $portal = $this->portalRepository->find($portalId);
            if ($portal) {
                $object->addPortal($portal);
            }
        }

        return null;
    }
}
