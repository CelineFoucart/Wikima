<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use ZipArchive;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class ImageAdminController extends AbstractController
{
    #[Route('/admin/app/image/download', name: 'app_image_download_all')]
    #[Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_SUPER_ADMIN') is_granted('ROLE_EDITOR')")]
    public function index()
    {
        $uploadedDir = $this->getParameter('kernel.project_dir') . '/public/uploads/';
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
        header('Content-Length: ' . filesize($zipname));
        readfile($zipname);
        unlink($zipname);
    }
}