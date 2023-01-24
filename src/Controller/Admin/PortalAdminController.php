<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Repository\CategoryRepository;
use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class PortalAdminController extends CRUDController
{
    public function __construct(
        private CategoryRepository $categoryRepository
    ) {
    }

    protected function preCreate(Request $request, object $object): ?Response
    {
        $categoryId = $request->query->getInt('category');
        if (0 === $categoryId) {
            return null;
        }

        $category = $this->categoryRepository->find($categoryId);
        if (!$category) {
            return null;
        }

        $object->addCategory($category);

        return null;
    }
}
