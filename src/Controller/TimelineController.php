<?php

namespace App\Controller;

use App\Entity\Timeline;
use App\Form\SearchType;
use App\Entity\Data\SearchData;
use App\Form\AdvancedSearchType;
use App\Repository\TimelineRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TimelineController extends AbstractController
{
    #[Route('/timelines', name: 'app_timeline_index')]
    public function index(TimelineRepository $timelineRepository, Request $request, int $perPageEven): Response
    {
        $page = $request->query->getInt('page', 1);
        $search = (new SearchData())->setPage($page);
        $form = $this->createForm(AdvancedSearchType::class, $search, ['allow_extra_fields' => true]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $timelines = $timelineRepository->search($search, $perPageEven);
        } else {
            $timelines = $timelineRepository->findPaginated($page, $perPageEven);
        }

        return $this->render('timeline/index.html.twig', [
            'timelines' => $timelines,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/timeline/{slug}', name: 'app_timeline_show')]
    #[Entity('timeline', expr: 'repository.findTimelineEventsBySlug(slug)')]
    public function show(Timeline $timeline): Response
    {
        return $this->render('timeline/show.html.twig', [
            'timeline' => $timeline,
            'form' => $this->createForm(SearchType::class, new SearchData())->createView(),
        ]);
    }
}
