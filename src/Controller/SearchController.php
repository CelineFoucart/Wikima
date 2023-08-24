<?php

namespace App\Controller;

use App\Entity\Data\SearchData;
use App\Repository\PlaceRepository;
use App\Repository\PersonRepository;
use App\Form\Search\GlobalSearchType;
use App\Repository\ArticleRepository;
use App\Repository\IdiomRepository;
use App\Repository\ImageRepository;
use App\Repository\TimelineRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SearchController extends AbstractController
{
    public function __construct(
        private ArticleRepository $articleRepository,
        private TimelineRepository $timelineRepository,
        private PersonRepository $personRepository,
        private PlaceRepository $placeRepository,
        private ImageRepository $imageRepository,
        private IdiomRepository $idiomRepository
    ) {
    }

    #[Route('/search', name: 'app_search')]
    public function searchAction(Request $request, bool $enableIdiom): Response
    {
        $search = (new SearchData())->setFields(['name', 'description', 'tags']);
        $form = $this->createForm(GlobalSearchType::class, $search);
        $form->handleRequest($request);
        $articles = [];
        $persons = [];
        $places = [];
        $timelines = [];
        $images = [];
        $idioms = [];
        
        if ($form->isSubmitted() && $form->isValid()) { 
            $articles = $this->articleRepository->advancedSearch($search);
            $timelines = $this->timelineRepository->advancedSearch($search);
            $places = $this->placeRepository->advancedSearch($search);
            $persons = $this->personRepository->advancedSearch($search);
            $images = $this->imageRepository->advancedSearch($search);

            if ($enableIdiom === true) {
                $idioms = $this->idiomRepository->advancedSearch($search);
            }
        }

        return $this->render('home/search.html.twig', [
            'form' => $form->createView(),
            'articles' => $articles,
            'persons' => $persons,
            'places' => $places,
            'timelines' => $timelines,
            'images' => $images,
            'idioms' => $idioms,
            'enableIdiom' => $enableIdiom,
            'search' => $search->getQuery(),
        ]);
    }
}