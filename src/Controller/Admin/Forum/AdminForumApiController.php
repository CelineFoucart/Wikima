<?php

declare(strict_types=1);

namespace App\Controller\Admin\Forum;

use App\Repository\ForumCategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\ExpressionLanguage\Expression;

class AdminForumApiController extends AbstractController
{
    public function __construct(private EntityManagerInterface $em)
    {
    }

    #[Route('/api/admin/forumcategories', 'api_category_forum_order', methods: ['POST'])]
    #[IsGranted(new Expression("is_granted('ROLE_ADMIN')"))]
    public function sortCategories(Request $request, ForumCategoryRepository $forumCategoryRepository, bool $enableForum): JsonResponse
    {
        if (false === $enableForum) {
            throw $this->createNotFoundException('Not Found');
        }

        $data = json_decode($request->getContent(), true);
        $categories = $forumCategoryRepository->findAll();

        foreach ($categories as $category) {
            $result = array_filter($data, function ($item) use ($category) {
                return $category->getId() === (int) $item['id'];
            });

            if (!empty($result)) {
                $result = array_values($result)[0];
                $position = $result['position'];
                $category->setPosition($position);
                $this->em->persist($category);
            }
        }

        $this->em->flush();

        return new JsonResponse('', 200);
    }
}
