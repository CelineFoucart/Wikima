<?php

declare(strict_types=1);

namespace App\Controller\Admin\Api;

use App\Entity\Map;
use App\Entity\MapPosition;
use App\Repository\PlaceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\ConstraintViolationListInterface;

#[Route('/admin/api/map')]
#[IsGranted(new Expression("is_granted('ROLE_ADMIN')"))]
final class AdminApiMapController extends AbstractController
{
    public function __construct(private SerializerInterface $serializer, private EntityManagerInterface $entityManager)
    {
    }

    #[Route('/{id}/append', name: 'api_append_position', methods: ['POST'])]
    public function appendAction(Request $request, Map $map, ValidatorInterface $validator, PlaceRepository $placeRepository): JsonResponse
    {
        /** @var MapPosition */
        $position = $this->serializer->deserialize($request->getContent(), MapPosition::class, 'json', ['groups' => 'index']);
        $position->setMap($map);

        $data = json_decode($request->getContent(), true);
        if (isset($data['placeId']) && $data['placeId'] !== null) {
            $place = $placeRepository->find($data['placeId']);
            $position->setPlace($place);
        }

        $errors = $this->getErrors($validator->validate($position));
        if (!empty($errors)) {
            return $this->json($errors, Response::HTTP_BAD_REQUEST);
        }

        $this->entityManager->persist($position);
        $this->entityManager->flush();

        return $this->json($position, Response::HTTP_CREATED, [], ['groups' => 'index']);
    }

    #[Route('-position/{id}', name: 'api_edit_position', methods: ['PUT'])]
    public function editAction(Request $request, MapPosition $position, ValidatorInterface $validator): JsonResponse
    {
        /** @var MapPosition */
        $this->serializer->deserialize(
            $request->getContent(), 
            MapPosition::class, 'json', ['groups' => 'index', AbstractNormalizer::OBJECT_TO_POPULATE => $position]
        );

        $errors = $this->getErrors($validator->validate($position));
        if (!empty($errors)) {
            return $this->json($errors, Response::HTTP_BAD_REQUEST);
        }

        $this->entityManager->persist($position);
        $this->entityManager->flush();

        return $this->json($position, Response::HTTP_OK, [], ['groups' => 'index']);
    }

    #[Route('-position/{id}/delete', name: 'api_delete_position', methods: ['DELETE'])]
    public function deleteAction(MapPosition $position): JsonResponse
    {
        $this->entityManager->remove($position);
        $this->entityManager->flush();

        return $this->json("", Response::HTTP_NO_CONTENT, [], ['groups' => 'index']);
    }

    private function getErrors(ConstraintViolationListInterface $violations): array
    {
        $errors = [];

        if (count($violations) > 0) {
            foreach ($violations as $violation) {
                $errors[$violation->getPropertyPath()][] = $violation->getMessage();
            }
        }

        return $errors;
    }
}
