<?php

namespace App\Controller;

use App\Entity\Timeline;
use App\Service\LogService;
use App\Entity\Data\SearchData;
use App\Form\Search\SearchType;
use App\Repository\TimelineRepository;
use App\Form\Search\AdvancedSearchType;
use App\Service\Word\WordTimelineGenerator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
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
    public function show(#[MapEntity(expr: 'repository.findTimelineEventsBySlug(slug)')] Timeline $timeline): Response
    {
        return $this->render('timeline/show.html.twig', [
            'timeline' => $timeline,
            'form' => $this->createForm(SearchType::class, new SearchData())->createView(),
        ]);
    }

    #[Route('/timeline/{slug}/word', name: 'app_timeline_word')]
    public function word(#[MapEntity(expr: 'repository.findTimelineEventsBySlug(slug)')] Timeline $timeline, WordTimelineGenerator $generator, LogService $logService): Response
    {
        try {
            $file = $generator->setTimeline($timeline)->generate();
            $response = new BinaryFileResponse($file['path']);
            $response->setContentDisposition(
                ResponseHeaderBag::DISPOSITION_ATTACHMENT,
                $file['filename']
            );
            $response->deleteFileAfterSend();

            return $response;
        } catch (\Exception $th) {
            $this->addFlash('error',"Le fichier n'a pas pu être généré.");
            $logService->error("Génération de '{$timeline->getSlug()}.docx'", $th->getMessage(), 'Timeline');
            
            return $this->redirectToRoute('app_timeline_show', ['slug' => $timeline->getSlug()]);
        }
    }
}
