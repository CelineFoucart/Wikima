<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\Image;
use App\Form\Admin\ImageType;
use App\Entity\Data\SearchData;
use App\Repository\ImageRepository;
use App\Form\Search\AdvancedImageSearchType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api/image')]
final class ImageController extends AbstractController
{
    public function __construct(private ImageRepository $imageRepository)
    {
    }

    #[Route('', name: 'api_image_index', methods:['GET'])]
    public function indexAction(Request $request, int $perPageEven): JsonResponse
    {
        $length = $request->query->getInt('length', $perPageEven);
        $params = [
            'categories' => $request->get('categories'),
            'portals' => $request->get('portals', []),
            'tags' => $request->get('tags', []),
            'query' => $request->get('query', null),
        ];

        $search = (new SearchData())->setPage($request->query->getInt('page', 1));
        $form = $this->createForm(AdvancedImageSearchType::class, $search);
        $form->submit($params);

        if (!$form->isValid()) {
            return $this->json('Invalid form', Response::HTTP_BAD_REQUEST);
        }

        /** @var SlidingPagination */
        $images = $this->imageRepository->search($search, [], $length);
        $data = ['medias' => $images, 'pagination' => $images->getPaginationData()];

        return $this->json($data, Response::HTTP_OK, [], ['groups' => 'index-media']);
    }

    #[Route('', name: 'api_image_create', methods:['POST'])]
    #[IsGranted(new Expression("is_granted('ROLE_ADMIN') or is_granted('ROLE_EDITOR')"))]
    public function createAction(Request $request): JsonResponse
    {
        $image = new Image();
        $form = $this->createForm(ImageType::class, $image, ['csrf_protection' => false]);
        $form->handleRequest($request);
        $data = array_merge($request->request->all(), ['imageFile' => ['file' => $request->files->get('imageFile')]]);
        $form->submit($data);
        
        if ($form->isValid()) {
            $image->setUpdatedAt(new \DateTime());
            $this->imageRepository->add($image, true);

            return $this->json($image, Response::HTTP_CREATED, [], ['groups' => 'index-media']);
        }

        return $this->json('Invalid data', Response::HTTP_BAD_REQUEST);
    }
}
