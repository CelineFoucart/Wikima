<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Backup;
use App\Entity\Category;
use App\Entity\Portal;
use App\Repository\BackupRepository;
use App\Service\BackupService;
use PhpZip\ZipFile;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin')]
final class AdminExportController extends AbstractController
{
    #[IsGranted(new Expression("is_granted('ROLE_SUPER_ADMIN')"))]
    #[Route('/image/download/all', name: 'admin_app_export_all')]
    public function exportAllAction(BackupService $backupService, BackupRepository $backupRepository): Response
    {
        $filename = $backupService->save()->getFilename();
        $backup = (new Backup())->setFilename($filename)->setCreatedAt($backupService->getDate());
        $backupRepository->add($backup);

        $zipFile = (new ZipFile())
            ->addFile($backupService->getBackupFolder().DIRECTORY_SEPARATOR.$backup->getFilename())
            ->addFile($this->getParameter('kernel.project_dir').'/.env.local');

        $uploadedDir = $this->getParameter('kernel.project_dir').'/public/uploads/';
        if (is_dir($uploadedDir)) {
            $zipFile->addEmptyDir('images');
            $finder = new Finder();
            $finder->files()->in($uploadedDir);
            foreach ($finder as $file) {
                $zipFile->addFile($file->getRealPath(), 'images/'.$file->getFilename());
            }
        }

        $faviconDir = $this->getParameter('kernel.project_dir').'/public/img/';
        if (is_dir($faviconDir)) {
            $zipFile->addEmptyDir('favicons-banner');
            $finderFavicon = new Finder();
            $finderFavicon->files()->in($faviconDir);
            foreach ($finderFavicon as $file) {
                $zipFile->addFile($file->getRealPath(), 'favicons-banner/'.$file->getFilename());
            }
        }

        $zipname = (new \DateTime())->format('Y-m-d').'.zip';
        $zipFile->saveAsFile($zipname)->close();

        return $this->returnAsBinaryResponse($zipname);
    }

    #[IsGranted(new Expression("is_granted('ROLE_ADMIN') or is_granted('ROLE_EDITOR')"))]
    #[Route('/image/download', name: 'admin_app_image_download')]
    public function downloadAction(): Response
    {
        $uploadedDir = $this->getParameter('kernel.project_dir').'/public/uploads/';
        if (!is_dir($uploadedDir)) {
            $this->addFlash('error', "Il n'y a aucune image à télécharger");

            return $this->redirectToRoute('admin_app_image_list');
        }

        $status = $this->download($uploadedDir);
        if (false === $status) {
            return $this->redirectToRoute('admin_app_image_list');
        }

        return $this->returnAsBinaryResponse();
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

        $zipname = 'favicons-banner.zip';
        $status = $this->download($uploadedDir, $zipname);
        if (false === $status) {
            return $this->redirectToRoute('admin_app_export');
        }

        return $this->returnAsBinaryResponse($zipname);
    }

    private function downloadGalleryEntity(object $entity, string $type): Response
    {
        if ($entity->getImages()->isEmpty() && null === $entity->getBanner()) {
            $this->addFlash('error', "Il n'y a aucune image à télécharger");

            return $this->redirectToRoute('admin_app_'.$type.'_show', ['id' => $entity->getId()]);
        }

        $uploadedDir = $this->getParameter('kernel.project_dir').'/public/uploads/';
        $zipname = $type.'-image-'.$entity->getSlug().'.zip';

        $zipFile = new ZipFile();
        foreach ($entity->getImages() as $image) {
            $zipFile->addFile($uploadedDir.$image->getFilename(), $image->getFilename());
        }

        if (null !== $entity->getBanner()) {
            $zipFile->addFile($uploadedDir.$entity->getBanner(), $entity->getBanner());
        }

        $zipFile->saveAsFile($zipname)->close();

        return $this->returnAsBinaryResponse($zipname);

        $response = new BinaryFileResponse($zipname);
        $response->headers->set('Content-Type', 'application/zip');
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $zipname);
        $response->deleteFileAfterSend(true);

        return $response;
    }

    private function download(string $directory, string $zipname = 'images.zip'): bool
    {
        $finder = new Finder();
        $finder->files()->in($directory);

        if (!$finder->hasResults()) {
            $this->addFlash('error', "Il n'y a aucun éléments à télécharger.");

            return false;
        }

        $zipFile = new ZipFile();
        foreach ($finder as $file) {
            $zipFile->addFile($file->getRealPath(), $file->getFilename());
        }
        $zipFile->saveAsFile($zipname)->close();

        return true;
    }

    private function returnAsBinaryResponse(string $zipname = 'images.zip')
    {
        $response = new BinaryFileResponse($zipname);
        $response->headers->set('Content-Type', 'application/zip');
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $zipname);
        $response->deleteFileAfterSend(true);

        return $response;
    }
}
