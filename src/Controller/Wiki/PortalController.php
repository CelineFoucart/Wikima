<?php

namespace App\Controller\Wiki;

use App\Entity\Note;
use App\Entity\User;
use App\Entity\Portal;
use App\Form\NoteType;
use App\Entity\ImageTag;
use App\Entity\PlaceType;
use App\Entity\PersonType;
use App\Entity\Data\SearchData;
use App\Form\Search\SearchType;
use App\Repository\ImageRepository;
use App\Repository\PlaceRepository;
use App\Repository\PersonRepository;
use App\Repository\PortalRepository;
use App\Repository\ArticleRepository;
use App\Repository\ImageTagRepository;
use App\Repository\PlaceTypeRepository;
use App\Repository\PersonTypeRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ArticleTypeRepository;
use App\Repository\ScenarioRepository;
use App\Service\AlphabeticalHelperService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class PortalController extends AbstractController
{
    public function __construct(
        private PortalRepository $portalRepository,
        private ArticleRepository $articleRepository,
        private PersonRepository $personRepository,
        private PlaceRepository $placeRepository
    ) {
    }

    #[Route('/portals/{slug}', name: 'app_portal_show')]
    public function portal(int $perPageOdd, #[MapEntity(expr: 'repository.findBySlug(slug)')] Portal $portal, Request $request, AlphabeticalHelperService $helper, ArticleTypeRepository $articleTypeRepository, PersonRepository $personRepository): Response
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

        $articles = $this->articleRepository->findByPortals([$portal], $page, $perPageOdd, $this->hidePrivate(), $type);

        return $this->render('portal/show_portal.html.twig', [
            'portal' => $portal,
            'articles' => $articles,
            'form' => $this->createForm(SearchType::class, new SearchData())->createView(),
            'items' => $helper->formatArray($articles->getItems()),
            'stickyArticles' => $this->articleRepository->findSticky($portal->getId()),
            'stickyPersons' => $personRepository->findSticky($portal->getId()),
            'stickyPlaces' => $this->placeRepository->findSticky($portal->getId()),
            'types' => $types,
            'type' => $type,
            'title' => $portal->getTitle(),
            'description' => $portal->getDescription(),
        ]);
    }

    #[Route('/portals', name: 'app_portal_index')]
    public function index(Request $request, int $perPageOdd): Response
    {
        $page = $request->query->getInt('page', 1);

        $portals = $this->portalRepository->findPaginated($page, $perPageOdd);

        return $this->render('portal/index_portal.html.twig', [
            'portals' => $portals,
            'form' => $this->createForm(SearchType::class, new SearchData())->createView(),
        ]);
    }

    #[Route('/portals/{slug}/gallery', name: 'app_portal_gallery')]
    public function gallery(int $perPageOdd, #[MapEntity(expr: 'repository.findBySlug(slug)')] Portal $portal, Request $request, ImageRepository $imageRepository, ImageTagRepository $imageTagRepository): Response
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

        return $this->render('portal/gallery_portal.html.twig', [
            'images' => $imageRepository->findByPortal($portal, $page, $perPageOdd, $typeId),
            'portal' => $portal,
            'form' => $this->createForm(SearchType::class, new SearchData())->createView(),
            'title' => $portal->getTitle(),
            'description' => $portal->getDescription(),
            'types' => $types,
            'type' => $type,
            'route_name' => 'app_image_index',
        ]);
    }

    #[Route('/portals/{slug}/persons', name: 'app_portal_persons')]
    public function persons(int $perPageOdd, #[MapEntity(expr: 'repository.findBySlug(slug)')] Portal $portal, Request $request, PersonTypeRepository $personTypeRepository): Response
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

        return $this->render('portal/persons_portal.html.twig', [
            'portal' => $portal,
            'persons' => $this->personRepository->findByParent($portal, 'portal', $page, $typeId, $perPageOdd),
            'form' => $this->createForm(SearchType::class, new SearchData())->createView(),
            'types' => $types,
            'type' => $type,
            'stickyElements' => $this->personRepository->findSticky($portal->getId()),
            'title' => $portal->getTitle(),
            'description' => $portal->getDescription(),
            'route_name' => 'app_person_index',
        ]);
    }

    #[Route('/portals/{slug}/places', name: 'app_portal_places')]
    public function places(int $perPageOdd, #[MapEntity(expr: 'repository.findBySlug(slug)')] Portal $portal, Request $request, PlaceTypeRepository $placeTypeRepository): Response
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

        return $this->render('portal/place_portal.html.twig', [
            'portal' => $portal,
            'places' => $this->placeRepository->findByParent($portal, 'portal', $page, $typeId, $perPageOdd),
            'form' => $this->createForm(SearchType::class, new SearchData())->createView(),
            'types' => $types,
            'type' => $type,
            'stickyElements' => $this->placeRepository->findSticky($portal->getId()),
            'title' => $portal->getTitle(),
            'description' => $portal->getDescription(),
            'route_name' => 'app_place_index',
        ]);
    }

    #[Route('/portals/{slug}/scenarios', name: 'app_portal_scenarios')]
    public function scenarios(int $perPageOdd, #[MapEntity(expr: 'repository.findBySlug(slug)')] Portal $portal, Request $request, ScenarioRepository $repository): Response
    {
        $page = $request->query->getInt('page', 1);

        return $this->render('portal/scenario_portal.html.twig', [
            'portal' => $portal,
            'scenarios' => $repository->findByParent([$portal], $page, $perPageOdd),
            'form' => $this->createForm(SearchType::class, new SearchData())->createView(),
            'title' => $portal->getTitle(),
            'description' => $portal->getDescription(),
            'route_name' => 'app_scenario_index',
        ]);
    }

    #[Route('/portals/{slug}/notes', name: 'app_portal_notes')]
    #[IsGranted(new Expression("is_granted('ROLE_ADMIN') or is_granted('ROLE_EDITOR')"))]
    public function notes(Portal $portal, Request $request, EntityManagerInterface $em): Response
    {
        $note = (new Note())->setPortal($portal);
        $noteForm = $this->createForm(NoteType::class, $note);
        $noteForm->handleRequest($request);
        
        if ($noteForm->isSubmitted() && $noteForm->isValid()) { 
            $note->setCreatedAt(new \DateTimeImmutable());
            $em->persist($note);
            $em->flush();

            return $this->redirectToRoute('app_portal_notes', ['slug' => $portal->getSlug()]);
        }

        return $this->render('portal/note_portal.html.twig', [
            'portal' => $portal,
            'form' => $this->createForm(SearchType::class, new SearchData())->createView(),
            'title' => $portal->getTitle(),
            'description' => $portal->getDescription(),
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
