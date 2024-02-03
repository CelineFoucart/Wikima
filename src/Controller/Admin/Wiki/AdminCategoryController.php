<?php

declare(strict_types=1);

namespace App\Controller\Admin\Wiki;

use App\Controller\Admin\AbstractAdminController;
use App\Entity\Category;
use App\Form\Admin\CategoryFormType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/category')]
#[IsGranted(new Expression("is_granted('ROLE_ADMIN')"))]
final class AdminCategoryController extends AbstractAdminController
{
    protected string $entityName = 'category';

    public function __construct(
        private CategoryRepository $categoryRepository
    ) {
    }

    #[Route('/', name: 'admin_app_category_list', methods: ['GET'])]
    public function listAction(): Response
    {
        return $this->render('Admin/category/list.html.twig', [
            'categories' => $this->categoryRepository->findAll(),
        ]);
    }

    #[Route('/create', name: 'admin_app_category_create', methods: ['GET', 'POST'])]
    public function createAction(Request $request): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryFormType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $category->setCreatedAt(new \DateTimeImmutable());
            $this->categoryRepository->add($category, true);
            $this->addFlash('success', 'La catégorie '.$category->getTitle().' a bien été créée.');

            return $this->redirectTo($request, $category->getId());
        }

        return $this->render('Admin/category/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/show', name: 'admin_app_category_show', methods: ['GET'])]
    public function showAction(Category $category): Response
    {
        return $this->render('Admin/category/show.html.twig', [
            'category' => $category,
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_app_category_edit', methods: ['GET', 'POST'])]
    public function editAction(Request $request, Category $category): Response
    {
        $form = $this->createForm(CategoryFormType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $category->setUpdatedAt(new \DateTime());
            $this->categoryRepository->add($category, true);
            $this->addFlash('success', 'La catégorie '.$category->getTitle().' a bien été modifiée.');

            return $this->redirectTo($request, $category->getId());
        }

        return $this->render('Admin/category/edit.html.twig', [
            'form' => $form->createView(),
            'category' => $category,
        ]);
    }

    #[Route('/{id}/delete', name: 'admin_app_category_delete', methods: ['POST'])]
    public function deleteAction(Request $request, Category $category, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isCsrfTokenValid('delete'.$category->getId(), $request->request->get('_token'))) {
            $this->addFlash('error', "Le token CSRF n'est pas valide!");
        }

        if (
            !$category->getPortals()->isEmpty()
            || !$category->getTimelines()->isEmpty()
            || !$category->getPeople()->isEmpty()
            || !$category->getPlaces()->isEmpty()
            || !$category->getPages()->isEmpty()
            || !$category->getImages()->isEmpty()
        ) {
            $this->addFlash('error', "La suppression a échoué, car cette catégorie contient des éléments de l'encylopédie ou des médias.");

            return $this->redirectToRoute('admin_app_category_show', ['id' => $category->getId()], Response::HTTP_SEE_OTHER);
        }

        if (!$category->getNotes()->isEmpty()) {
            foreach ($category->getNotes() as $note) {
                $entityManager->remove($note);
            }

            $entityManager->flush();
        }

        $this->categoryRepository->remove($category, true);
        $this->addFlash('success', 'La catégorie a été supprimée avec succès.');

        return $this->redirectToRoute('admin_app_category_list', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/admin/category/{id}/sort', name: 'admin_app_category_sort')]
    public function sortAction(Category $category): Response
    {
        return $this->render('Admin/category/sort.html.twig', [
            'category' => $category,
        ]);
    }
}
