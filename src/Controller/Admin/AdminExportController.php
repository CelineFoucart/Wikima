<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Entity\Portal;
use PhpZip\ZipFile;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin')]
final class AdminExportController extends AbstractController
{
    #[IsGranted(new Expression("is_granted('ROLE_ADMIN') or is_granted('ROLE_EDITOR')"))]
    #[Route('/image/download', name: 'admin_app_image_download')]
    public function downloadAction(): Response
    {
        $uploadedDir = $this->getParameter('kernel.project_dir').'/public/uploads/';
        if (!is_dir($uploadedDir)) {
            $this->addFlash('error', "Il n'y a aucune image à télécharger");

            return $this->redirectToRoute('admin_app_image_list');
        }

        $response = $this->download($uploadedDir);
        if (null === $response) {
            return $this->redirectToRoute('admin_app_image_list');
        }

        return $response;
    }

    #[IsGranted(new Expression("is_granted('ROLE_ADMIN') or is_granted('ROLE_EDITOR')"))]
    #[Route('/image/download-portal/{id}', name: 'admin_app_image_download_portal')]
    public function downloadPortalAction(Portal $portal): Response
    {
        return $this->downloadGalleryEntity($portal, 'portal');
    }

    #[IsGranted(new Expression("is_granted('ROLE_ADMIN') or is_granted('ROLE_EDITOR')"))]
    #[Route('/image/download-category/{id}', name: 'admin_app_image_download_category')]
    public function downloadCategoryAction(Category $category): Response
    {
        return $this->downloadGalleryEntity($category, 'category');
    }

    #[Route('/export-favicon-banner', name: 'admin_app_export_favicon')]
    #[IsGranted(new Expression("is_granted('ROLE_SUPER_ADMIN')"))]
    public function downloadFaviconAction(): Response
    {
        $uploadedDir = $this->getParameter('kernel.project_dir').'/public/img/';

        if (!is_dir($uploadedDir)) {
            $this->addFlash('error', "Il n'y a aucune image à télécharger");

            return $this->redirectToRoute('admin_app_export');
        }

        $response = $this->download($uploadedDir, 'favicons-banner.zip');
        if (null === $response) {
            return $this->redirectToRoute('admin_app_export');
        }

        return $response;
    }

    private function downloadGalleryEntity(object $entity, string $type): Response
    {
        if ($entity->getImages()->isEmpty() && $entity->getImageBanner() === null) {
            $this->addFlash('error', "Il n'y a aucune image à télécharger");

            return $this->redirectToRoute('admin_app_'.$type.'_show', ['id' => $entity->getId()]);
        }

        $uploadedDir = $this->getParameter('kernel.project_dir').'/public/uploads/';
        $zipname = $type . '-image-' . $entity->getSlug() . '.zip';

        $zipFile = new ZipFile();
        foreach ($entity->getImages() as $image) {
            $zipFile->addFile($uploadedDir . $image->getFilename(), $image->getFilename());
        }

        if ($entity->getImageBanner() !== null) {
            $zipFile->addFile($uploadedDir . $entity->getImageBanner(), $entity->getImageBanner());
        }

        $zipFile->saveAsFile($zipname)->close();
        $response = new BinaryFileResponse($zipname);
        $response->headers->set('Content-Type', 'application/zip');
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $zipname);
        $response->deleteFileAfterSend(true);

        return $response;
    }

    private function download(string $directory, string $zipname = 'images.zip'): ?BinaryFileResponse
    {
        $finder = new Finder();
        $finder->files()->in($directory);

        if (!$finder->hasResults()) {
            $this->addFlash('error',"Il n'y a aucun éléments à télécharger.");

            return null;
        }

        $zipFile = new ZipFile();
        foreach ($finder as $file) {
            $zipFile->addFile($file->getRealPath(), $file->getFilename());
        }
        $zipFile->saveAsFile($zipname)->close();

        $response = new BinaryFileResponse($zipname);
        $response->headers->set('Content-Type', 'application/zip');
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $zipname);
        $response->deleteFileAfterSend(true);

        return $response;
    }
}
