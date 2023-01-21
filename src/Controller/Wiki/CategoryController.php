<?php

namespace App\Controller\Wiki;

use App\Entity\Category;
use App\Entity\Data\SearchData;
use App\Entity\User;
use App\Form\SearchType;
use App\Repository\ArticleRepository;
use App\Repository\CategoryRepository;
use App\Repository\ImageRepository;
use App\Repository\PersonRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class CategoryController extends AbstractController
{
    public function __construct(
        private CategoryRepository $categoryRepository,
        private ArticleRepository $articleRepository
    ) {
    }

    #[Route('/category', name: 'app_category_index')]
    public function index(): Response
    {
        return $this->render('category/category_index.html.twig', [
            'categories' => $this->categoryRepository->findWithPortals(),
        ]);
    }

    #[Route('/category/{slug}', name: 'app_category_show')]
    public function category(Category $category, Request $request): Response
    {
        $page = $request->query->getInt('page', 1);
        $articles = $this->articleRepository->findByPortals($category->getPortals()->toArray(), $page, 10, $this->hidePrivate());

        return $this->render('category/show_category.html.twig', [
            'category' => $category,
            'articles' => $articles,
            'form' => $this->createForm(SearchType::class, new SearchData())->createView(),
        ]);
    }

    #[Route('/category/{slug}/gallery', name: 'app_category_gallery')]
    public function gallery(Category $category, Request $request, ImageRepository $imageRepository): Response
    {
        $page = $request->query->getInt('page', 1);

        return $this->render('category/category_gallery.html.twig', [
            'category' => $category,
            'images' => $imageRepository->findByCategory($category, $page),
            'form' => $this->createForm(SearchType::class, new SearchData())->createView(),
        ]);
    }

    #[Route('/category/{slug}/persons', name: 'app_category_persons')]
    public function persons(Category $category, Request $request, PersonRepository $personRepository): Response
    {
        $page = $request->query->getInt('page', 1);

        return $this->render('category/category_persons.html.twig', [
            'category' => $category,
            'persons' => $personRepository->findByParent('category', $category, $page),
            'form' => $this->createForm(SearchType::class, new SearchData())->createView(),
        ]);
    }

    private function hidePrivate(): bool
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            return true;
        }

        $roles = $user->getRoles();

        return !in_array('ROLE_ADMIN', $roles) || !in_array('ROLE_SUPER_ADMIN', $roles);
    }
}
