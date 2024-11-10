<?php

namespace App\Controller\Api;

use App\Entity\Image;
use App\Entity\Article;
use PhpOffice\PhpWord\Media;
use App\Security\Voter\VoterHelper;
use App\Repository\PortalRepository;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api/article')]
class ArticleController extends AbstractController
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }
    
    #[Route('', name: 'api_article_index', methods: ['GET'])]
    #[IsGranted(new Expression("is_granted('ROLE_ADMIN') or is_granted('ROLE_EDITOR')"))]
    public function indexAction(ArticleRepository $articleRepository, Request $request): JsonResponse
    {
        $parameters = $request->query->all();
        $recordsFiltered = $articleRepository->countSearchTotal($parameters);
        $recordsTotal = $articleRepository->countSearchTotal([]);

        $data = $articleRepository->searchPaginatedItems($parameters);

        $data = [
            'draw' => isset($parameters['draw']) ? (int) $parameters['draw'] : 0,
            'recordsFiltered' => isset($recordsFiltered['recordsFiltered']) ? $recordsFiltered['recordsFiltered'] : 0,
            'data' => $data,
            'recordsTotal' => isset($recordsTotal['recordsFiltered']) ? $recordsTotal['recordsFiltered'] : 0,
        ];

        return $this->json($data, 200, [], ['groups' => 'index']);
    }

    #[Route('/{id}/gallery', name: 'api_article_gallery', methods: ['GET'])]
    #[IsGranted(new Expression("is_granted('ROLE_ADMIN') or is_granted('ROLE_EDITOR')"))]
    public function galleryAction(Article $article): JsonResponse
    {
        return $this->json($article->getImages(), Response::HTTP_OK, [], ['groups' => 'index-media']);
    }

    #[Route('/{id}/gallery/{mediaId}', name: 'api_article_gallery_append', methods: ['POST'])]
    #[IsGranted(new Expression("is_granted('ROLE_ADMIN') or is_granted('ROLE_EDITOR')"))]
    public function appendMedia(#[MapEntity(id: 'id')] Article $article, #[MapEntity(id: 'mediaId')] Image $media): JsonResponse
    {
        $this->denyAccessUnlessGranted(VoterHelper::EDIT, $article);
        $article->addImage($media);
        $this->entityManager->persist($article);
        $this->entityManager->flush();

        return $this->json($media, Response::HTTP_OK, [], ['groups' => 'index-media']);
    }

    #[Route('/{id}/gallery/{mediaId}', name: 'api_article_gallery_remove', methods: ['DELETE'])]
    #[IsGranted(new Expression("is_granted('ROLE_ADMIN') or is_granted('ROLE_EDITOR')"))]
    public function removeMedia(#[MapEntity(id: 'id')] Article $article, #[MapEntity(id: 'mediaId')] Image $media): JsonResponse
    {
        $this->denyAccessUnlessGranted(VoterHelper::EDIT, $article);
        $article->removeImage($media);
        $this->entityManager->persist($article);
        $this->entityManager->flush();

        return $this->json("", Response::HTTP_NO_CONTENT);
    }
}
