<?php

declare(strict_types=1);

namespace App\Controller\Admin\Api;

use App\Entity\Note;
use App\Repository\ArticleRepository;
use App\Repository\PersonRepository;
use App\Repository\PlaceRepository;
use App\Repository\SectionRepository;
use App\Repository\TemplateRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;

class AdminApiController extends AbstractController
{
    public function __construct(private SerializerInterface $serializer, private EntityManagerInterface $em)
    {
    }

    #[Route('/api/admin/note/{id}/processed', 'api_note_processed', methods: ['POST'])]
    #[IsGranted(new Expression("is_granted('ROLE_ADMIN') or is_granted('ROLE_EDITOR')"))]
    public function updateNoteProcessed(Note $note): JsonResponse
    {
        $currentStatus = $note->getIsProcessed();
        $note->setIsProcessed(!$currentStatus);
        $this->em->persist($note);
        $this->em->flush();

        return new JsonResponse(
            $this->serializer->serialize(['id' => $note->getId()], 'json'),
            Response::HTTP_OK,
            [],
            true
        );
    }

    #[Route('/api/admin/persons', name: 'api_person_index', methods: ['GET'])]
    #[IsGranted(new Expression("is_granted('ROLE_ADMIN') or is_granted('ROLE_EDITOR')"))]
    public function personAction(PersonRepository $personRepository, Request $request): JsonResponse
    {
        $parameters = $request->query->all();
        $recordsFiltered = $personRepository->countSearchTotal($parameters);
        $recordsTotal = $personRepository->countSearchTotal([]);
        $data = [
            'draw' => isset($parameters['draw']) ? (int) $parameters['draw'] : 0,
            'recordsFiltered' => isset($recordsFiltered['recordsFiltered']) ? $recordsFiltered['recordsFiltered'] : 0,
            'data' => $personRepository->searchPaginatedItems($parameters),
            'recordsTotal' => isset($recordsTotal['recordsFiltered']) ? $recordsTotal['recordsFiltered'] : 0,
        ];

        return $this->json($data, 200, [], ['groups' => 'index']);
    }

    #[Route('/api/admin/sections', name: 'api_section_index', methods: ['GET'])]
    #[IsGranted(new Expression("is_granted('ROLE_ADMIN') or is_granted('ROLE_EDITOR')"))]
    public function sectionAction(SectionRepository $sectionRepository, Request $request): JsonResponse
    {
        $parameters = $request->query->all();
        $recordsFiltered = $sectionRepository->countSearchTotal($parameters);
        $recordsTotal = $sectionRepository->countSearchTotal([]);
        $data = [
            'draw' => isset($parameters['draw']) ? (int) $parameters['draw'] : 0,
            'recordsFiltered' => isset($recordsFiltered['recordsFiltered']) ? $recordsFiltered['recordsFiltered'] : 0,
            'data' => $sectionRepository->searchPaginatedItems($parameters),
            'recordsTotal' => isset($recordsTotal['recordsFiltered']) ? $recordsTotal['recordsFiltered'] : 0,
        ];

        return $this->json($data, 200, [], ['groups' => 'index']);
    }

    #[Route('/api/admin/place', name: 'api_place_index', methods: ['GET'])]
    #[IsGranted(new Expression("is_granted('ROLE_ADMIN') or is_granted('ROLE_EDITOR')"))]
    public function placeAction(PlaceRepository $placeRepository, Request $request): JsonResponse
    {
        $parameters = $request->query->all();
        $recordsFiltered = $placeRepository->countSearchTotal($parameters);
        $recordsTotal = $placeRepository->countSearchTotal([]);

        $data = [
            'draw' => isset($parameters['draw']) ? (int) $parameters['draw'] : 0,
            'recordsFiltered' => isset($recordsFiltered['recordsFiltered']) ? $recordsFiltered['recordsFiltered'] : 0,
            'data' => $placeRepository->searchPaginatedItems($parameters),
            'recordsTotal' => isset($recordsTotal['recordsFiltered']) ? $recordsTotal['recordsFiltered'] : 0,
        ];

        return $this->json($data, 200, [], ['groups' => 'index']);
    }

    #[Route('/api/admin/place/all', name: 'api_place_index_all', methods: ['GET'])]
    public function placeIndexAction(PlaceRepository $placeRepository): JsonResponse
    {
        return $this->json($placeRepository->findBy([], ['title' => 'asc']), 200, [], ['groups' => 'select']);
    }

    #[Route('/api/template', name: 'api_template_index', methods: ['GET'])]
    #[IsGranted(new Expression("is_granted('ROLE_ADMIN') or is_granted('ROLE_EDITOR')"))]
    public function templateIndex(TemplateRepository $templateRepository): JsonResponse
    {
        return $this->json($templateRepository->findBy([], ['title' => 'asc']), 200, [], ['groups' => 'index']);
    }
}
