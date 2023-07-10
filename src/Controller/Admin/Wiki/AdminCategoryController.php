<?php

declare(strict_types=1);

namespace App\Controller\Admin\Wiki;

use DateTime;
use DateTimeImmutable;
use App\Entity\Category;
use App\Repository\ImageRepository;
use App\Form\Admin\CategoryFormType;
use App\Repository\ArticleRepository;
use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Controller\Admin\AbstractAdminController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

#[Route('/admin/category')]
#[Security("is_granted('ROLE_ADMIN')")]
final class AdminCategoryController extends AbstractAdminController
{
    protected string $entityName = "category";

    public function __construct(
        private CategoryRepository $categoryRepository,
        private ImageRepository $imageRepository
    ) {
    }

    #[Route('/', name: 'admin_app_category_list', methods:['GET'])]
    public function listAction(): Response
    {
        return $this->render('Admin/category/list.html.twig', [
            'categories' => $this->categoryRepository->findAll(),
        ]);
    }

    #[Route('/create', name: 'admin_app_category_create', methods:['GET', 'POST'])]
    public function createAction(Request $request): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryFormType::class, $category);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) { 
            $category->setCreatedAt(new DateTimeImmutable());
            $this->categoryRepository->add($category, true);
            $this->addFlash('success', "La catégorie " . $category->getTitle() . " a bien été créée.");

            return $this->redirectTo($request, $category->getId());
        }

        return $this->render('Admin/category/create.html.twig', [
            'form' => $form->createView(),
            'images' => $this->imageRepository->findAll(),
        ]);
    }

    #[Route('/{id}/show', name: 'admin_app_category_show', methods:['GET'])]
    public function showAction(Category $category): Response
    {
        return $this->render('Admin/category/show.html.twig', [
            'category' => $category,
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_app_category_edit', methods:['GET', 'POST'])]
    public function editAction(Request $request, Category $category): Response
    {
        $form = $this->createForm(CategoryFormType::class, $category);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) { 
            $category->setUpdatedAt(new DateTime());
            $this->categoryRepository->add($category, true);
            $this->addFlash('success', "La catégorie " . $category->getTitle() . " a bien été modifiée.");

            return $this->redirectTo($request, $category->getId());
        }

        return $this->render('Admin/category/edit.html.twig', [
            'form' => $form->createView(),
            'category' => $category,
            'images' => $this->imageRepository->findAll(),
        ]);
    }

    #[Route('/{id}/delete', name: 'admin_app_category_delete', methods:['POST'])]
    public function deleteAction(Request $request, Category $category): Response
    {
        if ($this->isCsrfTokenValid('delete'.$category->getId(), $request->request->get('_token'))) {
            $this->categoryRepository->remove($category, true);
            $this->addFlash('success', "L'élément a été supprimé avec succès.");
        }

        return $this->redirectToRoute('admin_app_category_list', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/admin/category/{id}/sort', name: 'admin_app_category_sort')]
    public function sortAction(Category $category): Response
    {
        return $this->renderForm('Admin/category/sort.html.twig', [
            'category' => $category,
        ]);
    }
}