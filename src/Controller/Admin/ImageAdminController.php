<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Repository\CategoryRepository;
use App\Repository\PortalRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use ZipArchive;

#[Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_SUPER_ADMIN') or is_granted('ROLE_EDITOR')")]
final class ImageAdminController extends CRUDController
{
    public function __construct(
        private CategoryRepository $categoryRepository,
        private PortalRepository $portalRepository
    ) {
    }
    
    public function downloadAction()
    {
        $uploadedDir = $this->getParameter('kernel.project_dir').'/public/uploads/';
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

    protected function preCreate(Request $request, object $object): ?Response
    {
        $portalId = $request->query->getInt('portal');
        
        if ($portalId) {
            $portal = $this->portalRepository->find($portalId);
            if (!$portal) {
                return null;
            }

            $object->addPortal($portal);
            foreach ($portal->getCategories() as $category) {
                $object->addCategory($category);
            }
        }

        $categoryId = $request->query->getInt('category');
        if ($categoryId) {
            $category = $this->categoryRepository->find($categoryId);
            if (!$category) {
                return null;
            }

            $object->addCategory($category);
        }

        return null;
    }
}
