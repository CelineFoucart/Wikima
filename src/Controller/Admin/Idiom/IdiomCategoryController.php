<?php

declare(strict_types=1);

namespace App\Controller\Admin\Idiom;

use App\Entity\IdiomCategory;
use App\Form\Admin\IdiomCategoryType;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\IdiomCategoryRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Controller\Admin\AbstractAdminController;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\ExpressionLanguage\Expression;

#[Route('/idiom/category')]
#[IsGranted(new Expression("is_granted('ROLE_ADMIN')"))]
class IdiomCategoryController extends AbstractAdminController
{
    protected string $entityName = "idiom_category";

    public function __construct(private IdiomCategoryRepository $idiomCategoryRepository, bool $enableIdiom)
    {
        if (false === $enableIdiom) {
            throw $this->createNotFoundException('Not Found');
        }
    }

    #[Route('/', name: 'admin_app_idiom_category_list', methods: ['GET'])]
    public function listAction(): Response
    {
        return $this->render('Admin/idiom_category/list.html.twig', [
            'idiom_categories' => $this->idiomCategoryRepository->findBy([], ['position' => 'ASC']),
        ]);
    }

    #[Route('/create', name: 'admin_app_idiom_category_create', methods: ['GET', 'POST'])]
    public function createAction(Request $request, EntityManagerInterface $entityManager): Response
    {
        $idiomCategory = new IdiomCategory();
        $form = $this->createForm(IdiomCategoryType::class, $idiomCategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $position = $this->idiomCategoryRepository->findMaxPosition();
            $idiomCategory->setPosition($position+1);
            $entityManager->persist($idiomCategory);
            $entityManager->flush();
            $this->addFlash('success', "La catégorie a été créée.");

            return $this->redirectTo($request, $idiomCategory->getId());
        }

        return $this->render('Admin/idiom_category/create.html.twig', [
            'idiom_category' => $idiomCategory,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/show', name: 'admin_app_idiom_category_show', methods: ['GET'])]
    public function showAction(IdiomCategory $idiomCategory): Response
    {
        return $this->render('Admin/idiom_category/show.html.twig', [
            'idiom_category' => $idiomCategory,
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_app_idiom_category_edit', methods: ['GET', 'POST'])]
    public function editAction(Request $request, IdiomCategory $idiomCategory, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(IdiomCategoryType::class, $idiomCategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($idiomCategory);
            $entityManager->flush();
            $this->addFlash('success', "La catégorie a été modifiée.");

            return $this->redirectTo($request, $idiomCategory->getId());
        }

        return $this->render('Admin/idiom_category/edit.html.twig', [
            'idiom_category' => $idiomCategory,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'admin_app_idiom_category_delete', methods: ['POST'])]
    public function deleteAction(Request $request, IdiomCategory $idiomCategory, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$idiomCategory->getId(), $request->request->get('_token'))) {
            $entityManager->remove($idiomCategory);
            $entityManager->flush();
            $this->addFlash('success', "La catégorie a été supprimée.");
        }

        return $this->redirectToRoute('admin_app_idiom_category_list', [], Response::HTTP_SEE_OTHER);
    }
}
