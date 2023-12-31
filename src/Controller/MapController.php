<?php

namespace App\Controller;

use App\Entity\Map;
use App\Entity\Data\SearchData;
use App\Form\Search\SearchType;
use App\Form\Search\AdvancedPlaceSearchType;
use App\Repository\MapRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MapController extends AbstractController
{
    #[Route('/map', name: 'app_map_index')]
    public function index(Request $request, int $perPageEven, MapRepository $mapRepository): Response
    {
        $page = $request->query->getInt('page', 1);

        $search = (new SearchData())->setPage($page);
        $form = $this->createForm(AdvancedPlaceSearchType::class, $search, ['allow_extra_fields' => true]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $maps = $mapRepository->searchPaginated($search, $perPageEven);
        } else {
            $maps = $mapRepository->searchPaginated((new SearchData())->setPage($page), $perPageEven);
        }
        
        return $this->render('map/index.html.twig', [
            'form' => $form->createView(),
            'maps' => $maps,
        ]);
    }

    #[Route('/map/{slug}', name: 'app_map_show')]
    public function show(Map $map): Response
    {
        return $this->render('map/show.html.twig', [
            'map' => $map,
            'form' => $this->createForm(SearchType::class, new SearchData())->createView(),
        ]);
    }
}
