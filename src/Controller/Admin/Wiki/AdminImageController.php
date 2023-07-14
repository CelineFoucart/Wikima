<?php

declare(strict_types=1);

namespace App\Controller\Admin\Wiki;

use ZipArchive;
use App\Entity\Image;
use App\Repository\ImageRepository;
use App\Repository\PortalRepository;
use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Controller\Admin\AbstractAdminController;
use App\Form\Admin\ImageType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Finder\Finder;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;

#[Route('/admin/image')]
#[Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_SUPER_ADMIN') or is_granted('ROLE_EDITOR')")]
final class AdminImageController extends AbstractAdminController
{
    protected string $entityName = "image";

    public function __construct(
        private ImageRepository $imageRepository,
        private CacheManager $cacheManager
    ) {
    }

    #[Route('/', name: 'admin_app_image_list', methods:['GET'])]
    public function listAction(): Response
    {
        return $this->render('Admin/image/list.html.twig', [
            'images' => $this->imageRepository->findAll(),
        ]);
    }

    #[Route('/create', name: 'admin_app_image_create', methods:['GET', 'POST'])]
    public function createAction(Request $request, CategoryRepository $categoryRepository, PortalRepository $portalRepository): Response
    {
        $image = new Image();

        $categoryId = $request->query->getInt('category');
        if (0 !== $categoryId) {
            $category = $categoryRepository->find($categoryId);
            if ($category) {
                $image->addCategory($category);
            }
        }

        $portalId = $request->query->getInt('portal');
        if (0 !== $portalId) {
            $portal = $portalRepository->find($portalId);
            if ($portal) {
                $image->addPortal($portal);
            }
        }
        
        $form = $this->createForm(ImageType::class, $image);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) { 
            $image->setUpdatedAt(new \DateTime());
            $this->imageRepository->add($image, true);
            $this->addFlash('success', "L'image " . $image->getTitle() . " a bien été créée.");

            return $this->redirectTo($request, $image->getId());
        }

        return $this->render('Admin/image/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/show', name: 'admin_app_image_show', methods:['GET'])]
    public function showAction(Image $image): Response
    {
        return $this->render('Admin/image/show.html.twig', [
            'image' => $image,
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_app_image_edit', methods:['GET', 'POST'])]
    public function editAction(Request $request, Image $image): Response
    {
        $form = $this->createForm(ImageType::class, $image);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) { 
            $image->setUpdatedAt(new \DateTime());
            $this->imageRepository->add($image, true);
            $this->addFlash('success', "Les informations de l'image " . $image->getTitle() . " ont bien été modifiées.");

            return $this->redirectTo($request, $image->getId());
        }

        return $this->render('Admin/image/edit.html.twig', [
            'form' => $form->createView(),
            'image' => $image,
        ]);
    }

    #[Route('/{id}/delete', name: 'admin_app_image_delete', methods:['POST'])]
    public function deleteAction(Request $request, Image $image): Response
    {
        if ($this->isCsrfTokenValid('delete'.$image->getId(), $request->request->get('_token'))) {
            $this->cacheManager->remove('/uploads/'.$image->getFilename());
            $this->imageRepository->remove($image, true);
            $this->addFlash('success', "L'élément a été supprimé avec succès.");
        }

        return $this->redirectToRoute('admin_app_image_list', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/download', name: 'admin_app_image_download')]
    public function downloadAction()
    {
        $uploadedDir = $this->getParameter('kernel.project_dir').'/public/uploads/';

        if (!is_dir($uploadedDir)) {
            $this->addFlash('error', "Il n'y a aucune image à télécharger");

            return $this->redirectToRoute('admin_app_image_list');
        }

        $finder = new Finder();
        $finder->files()->in($uploadedDir);
        $zip = new ZipArchive();
        $zipname = 'images.zip';
        $zip->open($zipname, ZipArchive::CREATE);

        if ($finder->hasResults()) {
            foreach ($finder as $file) {
                $absoluteFilePath = $file->getRealPath();
                $zip->addFile($absoluteFilePath, $file->getFilename());
            }
        } else {
            $this->addFlash(
                'error',
                "Il n'y a aucune image à télécharger."
            );

            return $this->redirectToRoute('admin_app_image_list');
        }

        $zip->close();

        header('Content-Type: application/zip');
        header('Content-disposition: attachment; filename='.$zipname);
        header('Content-Length: '.filesize($zipname));
        readfile($zipname);
        unlink($zipname);
    }
}