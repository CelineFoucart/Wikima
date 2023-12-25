<?php

namespace App\Controller;

use App\Entity\Person;
use App\Entity\PersonType;
use App\Entity\Data\SearchData;
use App\Form\Search\SearchType;
use App\Repository\PersonRepository;
use App\Repository\PersonTypeRepository;
use App\Form\Search\AdvancedPersonSearchType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PersonController extends AbstractController
{
    public function __construct(
        private PersonRepository $personRepository,
    ) {
    }

    #[Route('/persons', name: 'app_person_index')]
    public function index(Request $request, int $perPageEven): Response
    {
        $search = (new SearchData())->setPage($request->query->getInt('page', 1));
        $form = $this->createForm(AdvancedPersonSearchType::class, $search, ['allow_extra_fields' => true]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $persons = $this->personRepository->search($search, $perPageEven);
        } else {
            $persons = $this->personRepository->findAllPaginated($search->getPage(), $perPageEven);
        }

        return $this->render('person/index.html.twig', [
            'persons' => $persons,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/persons/{slug}', name: 'app_person_show')]
    public function show(#[MapEntity(expr: 'repository.findBySlug(slug)')] Person $person): Response
    {
        return $this->render('person/show.html.twig', [
            'person' => $person,
            'form' => $this->createForm(SearchType::class, new SearchData())->createView(),
        ]);
    }

    #[Route('/type/persons/{slug}', name: 'app_person_type')]
    public function type(PersonType $personType, Request $request, int $perPageOdd, PersonTypeRepository $personTypeRepository): Response
    {
        $page = $request->query->getInt('page', 1);
        $persons = $this->personRepository->findByType($personType, $page, $perPageOdd);

        return $this->render('person/type.html.twig', [
            'persons' => $persons,
            'type' => $personType,
            'types' => $personTypeRepository->findAll(),
        ]);
    }
}
