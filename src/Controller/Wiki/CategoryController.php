<?php

namespace App\Controller\Wiki;

use App\Entity\User;
use App\Entity\Category;
use App\Form\Search\SearchType;
use App\Entity\PlaceType;
use App\Entity\PersonType;
use App\Entity\Data\SearchData;
use App\Entity\ImageTag;
use App\Entity\Note;
use App\Form\NoteType;
use App\Repository\ImageRepository;
use App\Repository\PlaceRepository;
use App\Repository\PersonRepository;
use App\Repository\PortalRepository;
use App\Repository\ArticleRepository;
use App\Repository\CategoryRepository;
use App\Repository\PlaceTypeRepository;
use App\Repository\PersonTypeRepository;
use App\Repository\ArticleTypeRepository;
use App\Repository\ImageTagRepository;
use App\Repository\ScenarioRepository;
use App\Service\AlphabeticalHelperService;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\ExpressionLanguage\Expression;

final class CategoryController extends AbstractController
{
    public function __construct(
        private CategoryRepository $categoryRepository,
        private ArticleRepository $articleRepository,
        private PersonRepository $personRepository,
        private PlaceRepository $placeRepository
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
            'title' => $category->getTitle(),
            'description' => $category->getDescription(),
            'stickyPlaces' => $this->placeRepository->findSticky(null, $category->getId()),
            'stickyPersons' => $this->personRepository->findSticky(null, $category->getId()),
        ]);
    }

    #[Route('/category/{slug}/articles', name: 'app_category_show_article')]
    public function article(int $perPageOdd, Category $category, Request $request, AlphabeticalHelperService $helper, ArticleTypeRepository $articleTypeRepository): Response
    {
        $types = $articleTypeRepository->findBy([], ['title' => 'ASC']);
        $page = $request->query->getInt('page', 1);
        $typeSlug = $request->query->get('type');
        $type = null;

        foreach ($types as $value) {
            if ($value->getSlug() === $typeSlug) {
                $type = $value;
            }
        }

        $portals = $category->getPortals()->toArray();
        $articles = $this->articleRepository->findByPortals($portals, $page, $perPageOdd, $this->hidePrivate(), $type);

        return $this->render('category/show_category_article.html.twig', [
            'category' => $category,
            'articles' => $articles,
            'form' => $this->createForm(SearchType::class, new SearchData())->createView(),
            'items' => $helper->formatArray($articles->getItems()),
            'types' => $types,
            'type' => $type,
            'title' => $category->getTitle(),
            'description' => $category->getDescription(),
        ]);
    }

    #[Route('/category/{slug}/gallery', name: 'app_category_gallery')]
    public function gallery(int $perPageOdd, Category $category, Request $request, ImageRepository $imageRepository, ImageTagRepository $imageTagRepository): Response
    {
        $page = $request->query->getInt('page', 1);
        $types = $imageTagRepository->findAll();
        $typeSlug = $request->query->get('type');

        $results = array_filter($types, function(ImageTag $imageTag) use ($typeSlug) {
            return $imageTag->getSlug() === $typeSlug;
        });

        if (!empty($results)) {
            $type = $results[array_key_first($results)];
            $typeId = $type->getId();
        } else {
            $type = null;
            $typeId = 0;
        }

        return $this->render('category/category_gallery.html.twig', [
            'category' => $category,
            'images' => $imageRepository->findByCategory($category, $page, $perPageOdd, $typeId),
            'form' => $this->createForm(SearchType::class, new SearchData())->createView(),
            'title' => $category->getTitle(),
            'description' => $category->getDescription(),
            'types' => $types,
            'type' => $type,
            'route_name' => 'app_image_index',
        ]);
    }

    #[Route('/category/{slug}/persons', name: 'app_category_persons')]
    public function persons(int $perPageOdd, Category $category, Request $request, PersonTypeRepository $personTypeRepository): Response
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
            'persons' => $this->personRepository->findByParent($category, 'category', $page, $typeId, $perPageOdd),
            'form' => $this->createForm(SearchType::class, new SearchData())->createView(),
            'types' => $types,
            'type' => $type,
            'stickyElements' => $this->personRepository->findSticky(null, $category->getId()),
            'title' => $category->getTitle(),
            'description' => $category->getDescription(),
            'route_name' => 'app_person_index',
        ]);
    }

    #[Route('/category/{slug}/places', name: 'app_category_places')]
    public function places(int $perPageOdd, Category $category, Request $request, PlaceTypeRepository $placeTypeRepository): Response
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
            'places' => $this->placeRepository->findByParent($category, 'category', $page, $typeId, $perPageOdd),
            'form' => $this->createForm(SearchType::class, new SearchData())->createView(),
            'types' => $types,
            'type' => $type,
            'stickyElements' => $this->placeRepository->findSticky(null, $category->getId()),
            'title' => $category->getTitle(),
            'description' => $category->getDescription(),
            'route_name' => 'app_place_index',
        ]);
    }

    #[Route('/category/{slug}/scenarios', name: 'app_category_scenarios')]
    public function scenarios(int $perPageOdd, Category $category, Request $request, ScenarioRepository $scenarioRepository): Response
    {
        $page = $request->query->getInt('page', 1);

        return $this->render('category/category_scenario.html.twig', [
            'category' => $category,
            'scenarios' => $scenarioRepository->findByParent($category->getPortals()->toArray(), $page, $perPageOdd),
            'form' => $this->createForm(SearchType::class, new SearchData())->createView(),
            'title' => $category->getTitle(),
            'description' => $category->getDescription(),
            'route_name' => 'app_scenario_index',
        ]);
    }

    #[Route('/category/{slug}/notes', name: 'app_category_notes')]
    #[IsGranted(new Expression("is_granted('ROLE_ADMIN') or is_granted('ROLE_EDITOR')"))]
    public function notes(Category $category, Request $request, EntityManagerInterface $em): Response
    {
        $note = (new Note())->setCategory($category);
        $noteForm = $this->createForm(NoteType::class, $note);
        $noteForm->handleRequest($request);
        
        if ($noteForm->isSubmitted() && $noteForm->isValid()) { 
            $note->setCreatedAt(new DateTimeImmutable());
            $em->persist($note);
            $em->flush();

            return $this->redirectToRoute('app_category_notes', ['slug' => $category->getSlug()]);
        }

        $portals = $category->getPortals()->toArray();

        return $this->render('category/category_note.html.twig', [
            'category' => $category,
            'form' => $this->createForm(SearchType::class, new SearchData())->createView(),
            'title' => $category->getTitle(),
            'description' => $category->getDescription(),
            'noteForm' => $noteForm->createView(),
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
