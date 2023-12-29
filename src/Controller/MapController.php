<?php

namespace App\Controller;

use App\Entity\Map;
use App\Entity\Data\SearchData;
use App\Form\Search\SearchType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MapController extends AbstractController
{
    #[Route('/map', name: 'app_map_index')]
    public function index(): Response
    {
        return $this->render('map/show.html.twig');
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
