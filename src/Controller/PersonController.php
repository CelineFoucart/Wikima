<?php

namespace App\Controller;

use App\Entity\Person;
use App\Entity\PersonType;
use App\Entity\Data\SearchData;
use App\Form\Search\SearchType;
use App\Repository\PersonRepository;
use App\Repository\PersonTypeRepository;
use App\Service\Word\WordPersonGenerator;
use App\Form\Search\AdvancedPersonSearchType;
use App\Service\LogService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
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

    #[Route('/persons/{slug}/word', name: 'app_person_word')]
    public function word(#[MapEntity(expr: 'repository.findBySlug(slug)')] Person $person, WordPersonGenerator $generator, LogService $logService): Response
    {
        try {
            $file = $generator->setPerson($person)->generate();
            $response = new BinaryFileResponse($file['path']);
            $response->setContentDisposition(
                ResponseHeaderBag::DISPOSITION_ATTACHMENT,
                $file['filename']
            );
            $response->deleteFileAfterSend();

            return $response;
        } catch (\Exception $th) {
            $this->addFlash('error',"Le fichier n'a pas pu être généré, car il y a des liens vers des images invalides.");
            $logService->error("Génération de '{$person->getSlug()}.docx'", $th->getMessage(), 'Person');
            
            return $this->redirectToRoute('app_person_show', ['slug' => $person->getSlug()]);
        }
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
