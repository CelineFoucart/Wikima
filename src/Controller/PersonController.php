<?php

namespace App\Controller;

use App\Entity\Data\SearchData;
use App\Entity\Person;
use App\Form\AdvancedSearchType;
use App\Repository\PersonRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PersonController extends AbstractController
{
    public function __construct(
        private PersonRepository $personRepository
    ) {
    }

    #[Route('/persons', name: 'app_person_index')]
    public function index(Request $request): Response
    {
        $search = (new SearchData())->setPage($request->query->getInt('page', 1));
        $form = $this->createForm(AdvancedSearchType::class, $search);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $persons = $this->personRepository->search($search);
        } else {
            $persons = $this->personRepository->findAllPaginated($search->getPage());
        }

        return $this->render('person/index.html.twig', [
            'persons' => $persons,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/persons/{slug}', name: 'app_person_show')]
    #[Entity('person', expr: 'repository.findBySlug(slug)')]
    public function show(Person $person): Response
    {
        return $this->render('person/show.html.twig', [
            'person' => $person,
        ]);
    }
}
