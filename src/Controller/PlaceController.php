<?php

namespace App\Controller;

use App\Entity\Data\SearchData;
use App\Entity\Place;
use App\Entity\PlaceType;
use App\Form\AdvancedSearchType;
use App\Repository\PlaceRepository;
use App\Repository\PlaceTypeRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PlaceController extends AbstractController
{
    private const PER_PAGE = 16;

    public function __construct(
        private PlaceTypeRepository $placeTypeRepository,
        private PlaceRepository $placeRepository
    ) {

    }

    #[Route('/places', name: 'app_place_index')]
    public function index(Request $request): Response
    {
        $search = (new SearchData())->setPage($request->query->getInt('page', 1));
        $form = $this->createForm(AdvancedSearchType::class, $search, ['allow_extra_fields' => true]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $places = $this->placeRepository->search($search, self::PER_PAGE);
        } else {
            $places = $this->placeRepository->findAllPaginated($search->getPage(), self::PER_PAGE);
        }

        return $this->render('place/index.html.twig', [
            'form' => $form->createView(),
            'places' => $places,
            'types' => $this->placeTypeRepository->findAll(),
        ]);
    }

    #[Route('/places/{slug}', name: 'app_place_show')]
    #[Entity('place', expr: 'repository.findBySlug(slug)')]
    public function show(Place $place): Response
    {
        return $this->render('place/show.html.twig', [
            'place' => $place,
        ]);
    }

    #[Route('/type/places/{slug}', name: 'app_place_type')]
    public function type(Request $request, PlaceType $placeType): Response
    {
        $page = $request->query->getInt('page', 1);
        $places = $this->placeRepository->findByType($placeType, $page, self::PER_PAGE);

        return $this->render('place/type.html.twig', [
            'places' => $places,
            'type' => $placeType,
            'types' => $this->placeTypeRepository->findAll(),
        ]);
    }
}
