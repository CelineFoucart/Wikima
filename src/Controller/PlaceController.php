<?php

namespace App\Controller;

use App\Entity\Place;
use App\Entity\PlaceType;
use App\Service\LogService;
use App\Entity\Data\SearchData;
use App\Form\Search\SearchType;
use App\Repository\PlaceRepository;
use App\Repository\PlaceTypeRepository;
use App\Service\Word\WordPlaceGenerator;
use App\Form\Search\AdvancedPlaceSearchType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PlaceController extends AbstractController
{
    public function __construct(
        private PlaceRepository $placeRepository
    ) {

    }

    #[Route('/places', name: 'app_place_index')]
    public function index(Request $request, int $perPageEven): Response
    {
        $search = (new SearchData())->setPage($request->query->getInt('page', 1));
        $form = $this->createForm(AdvancedPlaceSearchType::class, $search, ['allow_extra_fields' => true]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $places = $this->placeRepository->search($search, $perPageEven);
        } else {
            $places = $this->placeRepository->findAllPaginated($search->getPage(), $perPageEven);
        }

        return $this->render('place/index.html.twig', [
            'form' => $form->createView(),
            'places' => $places,
        ]);
    }

    #[Route('/places/{slug}', name: 'app_place_show')]
    public function show(#[MapEntity(expr: 'repository.findBySlug(slug)')]  Place $place): Response
    {
        return $this->render('place/show.html.twig', [
            'place' => $place,
            'form' => $this->createForm(SearchType::class, new SearchData())->createView(),
        ]);
    }

    #[Route('/places/{slug}/word', name: 'app_place_word')]
    public function word(#[MapEntity(expr: 'repository.findBySlug(slug)')] Place $place, WordPlaceGenerator $generator, LogService $logService): Response
    {
        try {
            $file = $generator->setPlace($place)->generate();
            $response = new BinaryFileResponse($file['path']);
            $response->setContentDisposition(
                ResponseHeaderBag::DISPOSITION_ATTACHMENT,
                $file['filename']
            );
            $response->deleteFileAfterSend();

            return $response;
        } catch (\Exception $th) {
            $this->addFlash('error',"Le fichier n'a pas pu être généré, car il y a des liens vers des images invalides.");
            $logService->error("Génération de '{$place->getSlug()}.docx'", $th->getMessage(), 'Place');
            
            return $this->redirectToRoute('app_place_show', ['slug' => $place->getSlug()]);
        }
    }

    #[Route('/type/places/{slug}', name: 'app_place_type')]
    public function type(Request $request, PlaceType $placeType, int $perPageOdd, PlaceTypeRepository $placeTypeRepository): Response
    {
        $page = $request->query->getInt('page', 1);
        $places = $this->placeRepository->findByType($placeType, $page, $perPageOdd);

        return $this->render('place/type.html.twig', [
            'places' => $places,
            'type' => $placeType,
            'types' => $placeTypeRepository->findAll(),
        ]);
    }
}
