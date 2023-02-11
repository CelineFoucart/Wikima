<?php

namespace App\Controller\Wiki;

use App\Entity\Category;
use App\Entity\Data\SearchData;
use App\Entity\PersonType;
use App\Entity\PlaceType;
use App\Entity\User;
use App\Form\SearchType;
use App\Repository\ArticleRepository;
use App\Repository\CategoryRepository;
use App\Repository\ImageRepository;
use App\Repository\PersonRepository;
use App\Repository\PersonTypeRepository;
use App\Repository\PlaceRepository;
use App\Repository\PlaceTypeRepository;
use App\Repository\PortalRepository;
use App\Service\AlphabeticalHelperService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
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
    public function category(Category $category, PortalRepository $portalRepository): Response
    {
        return $this->render('category/show_category.html.twig', [
            'category' => $category,
            'form' => $this->createForm(SearchType::class, new SearchData())->createView(),
            'portals' => $portalRepository->findByCategory($category),
        ]);
    }

    #[Route('/category/{slug}/articles', name: 'app_category_show_article')]
    public function article(Category $category, Request $request, AlphabeticalHelperService $helper): Response
    {
        $page = $request->query->getInt('page', 1);
        $articles = $this->articleRepository->findByPortals($category->getPortals()->toArray(), $page, 30, $this->hidePrivate());

        return $this->render('category/show_category_article.html.twig', [
            'category' => $category,
            'articles' => $articles,
            'form' => $this->createForm(SearchType::class, new SearchData())->createView(),
            'items' => $helper->formatArray($articles->getItems()),
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
    public function persons(Category $category, Request $request, PersonRepository $personRepository, PersonTypeRepository $personTypeRepository): Response
    {
        $types = $personTypeRepository->findAll();
        $page = $request->query->getInt('page', 1);
        $typeSlug = $request->query->get('type');

        $results = array_filter($types, function(PersonType $personType) use ($typeSlug) {
            return $personType->getSlug() === $typeSlug;
        });

        if (!empty($results)) {
            $type = $results[array_key_first($results)];
            $typeId = $type->getId();
        } else {
            $type = null;
            $typeId = 0;
        }

        return $this->render('category/category_persons.html.twig', [
            'category' => $category,
            'persons' => $personRepository->findByParent($category, 'category', $page, $typeId, 21),
            'form' => $this->createForm(SearchType::class, new SearchData())->createView(),
            'types' => $types,
            'type' => $type,
            'stickyElements' => $personRepository->findSticky(null, $category->getId()),
        ]);
    }

    #[Route('/category/{slug}/places', name: 'app_category_places')]
    public function places(Category $category, Request $request, PlaceRepository $placeRepository, PlaceTypeRepository $placeTypeRepository): Response
    {
        $types = $placeTypeRepository->findAll();
        $page = $request->query->getInt('page', 1);
        $typeSlug = $request->query->get('type');

        $results = array_filter($types, function(PlaceType $placeType) use ($typeSlug) {
            return $placeType->getSlug() === $typeSlug;
        });

        if (!empty($results)) {
            $type = $results[array_key_first($results)];
            $typeId = $type->getId();
        } else {
            $type = null;
            $typeId = 0;
        }

        return $this->render('category/category_places.html.twig', [
            'category' => $category,
            'places' => $placeRepository->findByParent($category, 'category', $page, $typeId),
            'form' => $this->createForm(SearchType::class, new SearchData())->createView(),
            'types' => $types,
            'type' => $type,
            'stickyElements' => $placeRepository->findSticky(null, $category->getId()),
        ]);
    }

    #[Route('/category/{slug}/notes', name: 'app_category_notes')]
    #[Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_EDITOR')")]
    public function notes(Category $category): Response
    {
        return $this->render('category/category_note.html.twig', [
            'category' => $category,
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
