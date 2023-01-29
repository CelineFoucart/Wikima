<?php

namespace App\Controller\Wiki;

use App\Entity\Data\SearchData;
use App\Entity\Portal;
use App\Entity\User;
use App\Form\SearchType;
use App\Repository\ArticleRepository;
use App\Repository\ImageRepository;
use App\Repository\PersonRepository;
use App\Repository\PortalRepository;
use App\Repository\TimelineRepository;
use App\Service\AlphabeticalHelperService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class PortalController extends AbstractController
{
    public function __construct(
        private PortalRepository $portalRepository,
        private ArticleRepository $articleRepository
    ) {
    }

    #[Route('/portals/{slug}', name: 'app_portal_show')]
    #[Entity('portals', expr: 'repository.findBySlug(slug)')]
    public function portal(Portal $portal, Request $request, AlphabeticalHelperService $helper): Response
    {
        $page = $request->query->getInt('page', 1);
        $articles = $this->articleRepository->findByPortals([$portal], $page, 16, $this->hidePrivate());

        return $this->render('portal/show_portal.html.twig', [
            'portal' => $portal,
            'articles' => $articles,
            'form' => $this->createForm(SearchType::class, new SearchData())->createView(),
            'items' => $helper->formatArray($articles->getItems()),
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
    #[Entity('portals', expr: 'repository.findBySlug(slug)')]
    public function gallery(Portal $portal, Request $request, ImageRepository $imageRepository): Response
    {
        $page = $request->query->getInt('page', 1);

        return $this->render('portal/gallery_portal.html.twig', [
            'images' => $imageRepository->findByPortal($portal, $page),
            'portal' => $portal,
            'form' => $this->createForm(SearchType::class, new SearchData())->createView(),
        ]);
    }

    #[Route('/portals/{slug}/timelines', name: 'app_portal_timelines')]
    #[Entity('portals', expr: 'repository.findBySlug(slug)')]
    public function timelines(Portal $portal, Request $request, TimelineRepository $timelineRepository): Response
    {
        $page = $request->query->getInt('page', 1);

        return $this->render('portal/timelines_portal.html.twig', [
            'timelines' => $timelineRepository->findTimelinesByPortal($portal, $page),
            'portal' => $portal,
            'form' => $this->createForm(SearchType::class, new SearchData())->createView(),
        ]);
    }

    #[Route('/portals/{slug}/persons', name: 'app_portal_persons')]
    #[Entity('portals', expr: 'repository.findBySlug(slug)')]
    public function persons(Portal $portal, Request $request, PersonRepository $personRepository): Response
    {
        $page = $request->query->getInt('page', 1);

        return $this->render('portal/persons_portal.html.twig', [
            'portal' => $portal,
            'persons' => $personRepository->findByParent('category', $portal, $page),
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
