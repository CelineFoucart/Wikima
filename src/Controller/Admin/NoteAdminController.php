<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Repository\CategoryRepository;
use App\Repository\PortalRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class NoteAdminController extends CRUDController
{
    public function __construct(
        private EntityManagerInterface $em, 
        private PortalRepository $portalRepository,
        private CategoryRepository $categoryRepository
    ) {
    }

    protected function preCreate(Request $request, object $object): ?Response
    {
        $portalId = $request->query->getInt('portal');
        if (0 !== $portalId) {
            $portal = $this->portalRepository->find($portalId);
            if ($portal) {
                $object->setPortal($portal);
            }
        }

        $categoryId = $request->query->getInt('category');
        if (0 !== $categoryId) {
            $category = $this->categoryRepository->find($categoryId);
            if ($category) {
                $object->setCategory($category);
            }
        }



        return null;
    }

}