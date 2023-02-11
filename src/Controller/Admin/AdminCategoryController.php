<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sonata\AdminBundle\Controller\CRUDController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

#[Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_SUPER_ADMIN')")]
class AdminCategoryController extends CRUDController
{
    public function __construct(
        private CategoryRepository $categoryRepository,
    ) {
    }

    public function sortableAction(?int $id): Response
    {
        $category = $this->categoryRepository->find($id);

        if (!$category instanceof Category) {
            throw new NotFoundHttpException(sprintf('unable to find the object with id: %s', $id));
        }

        return $this->renderForm('Admin/category_sortables.html.twig', [
            'category' => $category,
        ]);
    }
}