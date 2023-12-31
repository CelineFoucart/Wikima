<?php

declare(strict_types=1);

namespace App\Controller\Admin\Place;

use App\Entity\Map;
use App\Entity\Data\SearchData;
use App\Form\Admin\MapFormType;
use App\Repository\MapRepository;
use App\Form\Search\AdvancedSearchType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Controller\Admin\AbstractAdminController;
use App\Repository\ImageRepository;
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
    public function createAction(Request $request, ImageRepository $imageRepository): Response
    {
        $map = new Map();

        $imageId = $request->query->getInt('image');
        $image = null;
        
        if (0 !== $imageId) {
            $image = $imageRepository->find($imageId);
            if ($image) {
                $map->setImage($image)
                    ->setTitle($image->getTitle())
                    ->setSlug($image->getSlug())
                    ->setDescription($image->getDescription())
                ;

                foreach ($image->getCategories() as $category) {
                    $map->addCategory($category);
                }

                foreach ($image->getPortals() as $portal) {
                    $map->addPortal($portal);
                }
            }
        }

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
            'map' => $map,
        ]);
    }

    #[Route('/{id}/show', name: 'admin_app_map_show', methods:['GET'])]
    public function showAction(Map $map): Response
    {
        return $this->render('Admin/map/show_general.html.twig', [
            'map' => $map,
            'general_active' => true,
        ]);
    }

    #[Route('/{id}/image', name: 'admin_app_map_image', methods:['GET', 'POST'])]
    public function imageAction(Request $request, Map $map, ImageRepository $imageRepository): Response
    {
        $page = $request->query->getInt('page', 1);
        $excludes = [];
        if ($map->getImage()) {
            $excludes[] = $map->getImage()->getId();
        }

        if ($request->isMethod('POST')) {
            $imageId = $request->request->getInt('imageId');
            $image = $imageRepository->find($imageId);

            if (null === $image) {
                $this->addFlash('error', "L'image que vous avez choisi n'existe pas.");
            } else {
                $map->setImage($image);
                $this->mapRepository->save($map, true);
                $this->addFlash('success', "L'image a bien été ajoutée.");
            }

            return $this->redirectToRoute('admin_app_map_show', ['id' => $map->getId()]);
        }

        $searchData = (new SearchData())->setPage($page);
        $searchForm = $this->createForm(AdvancedSearchType::class, $searchData, ['allow_extra_fields' => true]);
        $searchForm->handleRequest($request);

        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
            $images = $imageRepository->search($searchData, $excludes, 15);
        } else {
            $images = $imageRepository->findPaginated($page, $excludes, 15);
        }

        return $this->render('Admin/map/show_image.html.twig', [
            'map' => $map,
            'images' => $images,
            'searchForm' => $searchForm->createView(),
            'image_active' => true,
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

    #[Route('/{id}/delete', name: 'admin_app_map_delete', methods:['POST'])]
    public function deleteAction(Request $request, Map $map): Response
    {
        if ($this->isCsrfTokenValid('delete'.$map->getId(), $request->request->get('_token'))) {
            $this->mapRepository->remove($map, true);
            $this->addFlash('success', "La carte a été supprimée avec succès.");
        }

        return $this->redirectToRoute('admin_app_map_list', [], Response::HTTP_SEE_OTHER);
    }
}
