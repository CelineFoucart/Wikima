<?php

namespace App\Controller\Wiki;

use App\Entity\User;
use App\Entity\Portal;
use App\Form\SearchType;
use App\Entity\PlaceType;
use App\Entity\PersonType;
use App\Entity\Data\SearchData;
use App\Repository\ImageRepository;
use App\Repository\PlaceRepository;
use App\Repository\PersonRepository;
use App\Repository\PortalRepository;
use App\Repository\ArticleRepository;
use App\Repository\TimelineRepository;
use App\Repository\PlaceTypeRepository;
use App\Repository\PersonTypeRepository;
use App\Repository\ArticleTypeRepository;
use App\Service\AlphabeticalHelperService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class PortalController extends AbstractController
{
    public function __construct(
        private PortalRepository $portalRepository,
        private ArticleRepository $articleRepository
    ) {
    }

    #[Route('/portals/{slug}', name: 'app_portal_show')]
    #[Entity('portal', expr: 'repository.findBySlug(slug)')]
    public function portal(Portal $portal, Request $request, AlphabeticalHelperService $helper, ArticleTypeRepository $articleTypeRepository): Response
    {
        $types = $articleTypeRepository->findAll();
        $page = $request->query->getInt('page', 1);
        $typeSlug = $request->query->get('type');
        $type = null;

        foreach ($types as $value) {
            if ($value->getSlug() === $typeSlug) {
                $type = $value;
            }
        }

        $articles = $this->articleRepository->findByPortals([$portal], $page, 30, $this->hidePrivate(), $type);

        return $this->render('portal/show_portal.html.twig', [
            'portal' => $portal,
            'articles' => $articles,
            'form' => $this->createForm(SearchType::class, new SearchData())->createView(),
            'items' => $helper->formatArray($articles->getItems()),
            'stickyElements' => $this->articleRepository->findSticky($portal->getId()),
            'types' => $types,
            'type' => $type,
        ]);
    }

    #[Route('/portals', name: 'app_portal_index')]
    public function index(Request $request, AlphabeticalHelperService $helper): Response
    {
        $page = $request->query->getInt('page', 1);

        $portals = $this->portalRepository->findPaginated($page);

        return $this->render('portal/index_portal.html.twig', [
            'portals' => $portals,
            'form' => $this->createForm(SearchType::class, new SearchData())->createView(),
            'items' => $helper->formatArray($portals->getItems()),
        ]);
    }

    #[Route('/portals/{slug}/gallery', name: 'app_portal_gallery')]
    #[Entity('portal', expr: 'repository.findBySlug(slug)')]
    public function gallery(Portal $portal, Request $request, ImageRepository $imageRepository): Response
    {
        $page = $request->query->getInt('page', 1);

        return $this->render('portal/gallery_portal.html.twig', [
            'images' => $imageRepository->findByPortal($portal, $page),
            'portal' => $portal,
            'form' => $this->createForm(SearchType::class, new SearchData())->createView(),
        ]);
    }

    #[Route('/portals/{slug}/persons', name: 'app_portal_persons')]
    #[Entity('portal', expr: 'repository.findBySlug(slug)')]
    public function persons(Portal $portal, Request $request, PersonRepository $personRepository, PersonTypeRepository $personTypeRepository): Response
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
            'persons' => $personRepository->findByParent($portal, 'portal', $page, $typeId, 21),
            'form' => $this->createForm(SearchType::class, new SearchData())->createView(),
            'types' => $types,
            'type' => $type,
            'stickyElements' => $personRepository->findSticky($portal->getId()),
        ]);
    }

    #[Route('/portals/{slug}/places', name: 'app_portal_places')]
    #[Entity('portals', expr: 'repository.findBySlug(slug)')]
    public function place(Portal $portal, Request $request, PlaceTypeRepository $placeTypeRepository, PlaceRepository $placeRepository): Response
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
            'places' => $placeRepository->findByParent($portal, 'portal', $page, $typeId, 21),
            'form' => $this->createForm(SearchType::class, new SearchData())->createView(),
            'types' => $types,
            'type' => $type,
            'stickyElements' => $placeRepository->findSticky($portal->getId()),
        ]);
    }

    #[Route('/portals/{slug}/notes', name: 'app_portal_notes')]
    #[Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_EDITOR')")]
    public function notes(Portal $portal): Response
    {
        return $this->render('portal/note_portal.html.twig', [
            'portal' => $portal,
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
