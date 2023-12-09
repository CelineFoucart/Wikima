<?php

declare(strict_types=1);

namespace App\Controller\Admin\Image;

use App\Entity\Image;
use App\Entity\ImageGroup;
use App\Form\Admin\ImageType;
use App\Entity\Data\SearchData;
use App\Repository\ImageRepository;
use App\Form\Admin\ImageGroupFormType;
use App\Form\Search\AdvancedSearchType;
use App\Repository\ImageGroupRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Controller\Admin\AbstractAdminController;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/image/group')]
#[IsGranted(new Expression("is_granted('ROLE_ADMIN') or is_granted('ROLE_EDITOR')"))]
final class AdminImageGroupController extends AbstractAdminController
{
    protected string $entityName = "image_group";

    #[Route('/', name: 'admin_app_image_group_list', methods:['GET'])]
    public function listAction(ImageGroupRepository $imageGroupRepository): Response
    {
        return $this->render('Admin/image_group/list.html.twig', [
            'image_groups' => $imageGroupRepository->findAll(),
        ]);
    }

    #[Route('/create', name: 'admin_app_image_group_create', methods:['GET', 'POST'])]
    public function createAction(Request $request, EntityManagerInterface $entityManager): Response
    {
        $imageGroup = new ImageGroup();
        $form = $this->createForm(ImageGroupFormType::class, $imageGroup);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($imageGroup);
            $entityManager->flush();
            $this->addFlash('success', "Le groupe " . $imageGroup . " a bien été créé.");

            return $this->redirectTo($request, $imageGroup->getId());
        }

        return $this->render('Admin/image_group/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/show', name: 'admin_app_image_group_show', methods:['GET', 'POST'])]
    public function showAction(ImageGroup $imageGroup, Request $request, ImageRepository $imageRepository, EntityManagerInterface $em): Response
    {
        $page = $request->query->getInt('page', 1);
        $image = (new Image())->setPortals($imageGroup->getPortals());
        $formImage = $this->createForm(ImageType::class, $image);
        $formImage->handleRequest($request);

        if ($formImage->isSubmitted()) {
            if ($formImage->isValid()) {
                $imageRepository->add($image, true);
                $imageGroup->addImage($image);
                $em->persist($imageGroup);
                $em->flush();
                $this->addFlash('success', "L'image a bien été ajoutée.");
                return $this->redirectToRoute('admin_app_image_group_show',  ['id' => $imageGroup->getId()]);
            } else {
                $this->addFlash('error',  "La soumission a échoué, car le formulaire n'est pas valide.");
            }
        } elseif ('POST' === $request->getMethod()) {
            $this->handleGallery($request, $imageGroup, $imageRepository, $em);
            $uri = $request->server->get('REQUEST_URI');

            if ($uri) {
                return $this->redirect($uri);
            }

            return $this->redirectToRoute('admin_app_image_group_show', ['id' => $imageGroup->getId()]);
        }

        $excludes = array_map(function (Image $item) {
            return $item->getId();
        }, $imageGroup->getImages()->toArray());
        $searchData = (new SearchData())->setPage($page);
        $form = $this->createForm(AdvancedSearchType::class, $searchData, ['allow_extra_fields' => true]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $images = $imageRepository->search($searchData, $excludes, 15);
        } else {
            $images = $imageRepository->findPaginated($page, $excludes, 15);
        }

        return $this->render('Admin/image_group/show.html.twig', [
            'image_group' => $imageGroup,
            'images' => $images,
            'form' => $form->createView(),
            'formImage' => $formImage->createView(),
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_app_image_group_edit', methods:['GET', 'POST'])]
    public function editAction(Request $request, ImageGroup $imageGroup, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ImageGroupFormType::class, $imageGroup);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) { 
            $entityManager->persist($imageGroup);
            $entityManager->flush();
            $this->addFlash('success', "Le groupe " . $imageGroup . " a bien été modifié.");

            return $this->redirectTo($request, $imageGroup->getId());
        }

        return $this->render('Admin/image_group/edit.html.twig', [
            'form' => $form->createView(),
            'image_group' => $imageGroup,
        ]);
    }

    #[Route('/{id}/delete', name: 'admin_app_image_group_delete', methods:['POST'])]
    public function deleteAction(Request $request, ImageGroup $imageGroup, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$imageGroup->getId(), $request->request->get('_token'))) {
            $entityManager->remove($imageGroup);
            $entityManager->flush();
            $this->addFlash('success', "Le groupe a été supprimé avec succès.");
        }

        return $this->redirectToRoute('admin_app_image_group_list', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * Update the gallery, adding or removing an image.
     */
    private function handleGallery(Request $request, ImageGroup $imageGroup, ImageRepository $imageRepository, EntityManagerInterface $em): void
    {
        $imageId = $request->request->getInt('imageId');
        $image = $imageRepository->find($imageId);

        if (null === $image) {
            $this->addFlash('error', "L'image que vous avez choisi n'existe pas.");
        }

        $delete = $request->request->get('delete');

        if ($this->isCsrfTokenValid('image'.$image->getId(), $request->request->get('_token'))) {
            if (null === $delete) {
                $imageGroup->addImage($image);
                $em->persist($imageGroup);
                $em->flush();
                $this->addFlash('success', "L'image a bien été ajoutée à l'article.");
            } else {
                $imageGroup->removeImage($image);
                $em->persist($imageGroup);
                $em->flush();
                $this->addFlash('success', "L'image a bien été enlevée de l'article.");
            }
        }
    }
}
