<?php

declare(strict_types=1);

namespace App\Controller\Admin\Api;

use App\Entity\Map;
use App\Entity\MapPosition;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/api/map')]
#[IsGranted(new Expression("is_granted('ROLE_ADMIN')"))]
final class AdminApiMapController extends AbstractController
{
    public function __construct(private SerializerInterface $serializer, private EntityManagerInterface $entityManager)
    {
    }

    #[Route('/{id}/append', name: 'api_append_position', methods: ['POST'])]
    public function appendAction(Request $request, Map $map, ValidatorInterface $validator): JsonResponse
    {
        /** @var MapPosition */
        $position = $this->serializer->deserialize($request->getContent(), MapPosition::class, 'json', ['groups' => 'index']);
        $position->setMap($map);

        $violations = $validator->validate($position);

        if (count($violations) > 0) {
            $errors = [];

            foreach ($violations as $violation) {
                $errors[$violation->getPropertyPath()][] = $violation->getMessage();
            }

            return $this->json($errors, Response::HTTP_BAD_REQUEST);
        }

        $this->entityManager->persist($position);
        $this->entityManager->flush();

        return $this->json($position, Response::HTTP_CREATED, [], ['groups' => 'index']);
    }
}
