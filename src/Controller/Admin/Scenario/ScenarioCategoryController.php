<?php

namespace App\Controller\Admin\Scenario;

use App\Entity\ScenarioCategory;
use App\Form\Admin\ScenarioCategoryType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\ScenarioCategoryRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Controller\Admin\AbstractAdminController;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/scenario/category')]
#[IsGranted(new Expression("is_granted('ROLE_ADMIN') or is_granted('ROLE_EDITOR')"))]
class ScenarioCategoryController extends AbstractAdminController
{
    protected string $entityName = 'scenario_category';

    public function __construct(bool $enableScenario)
    {
        if (false === $enableScenario) {
            throw $this->createNotFoundException('Not Found');
        }
    }

    #[Route('/', name: 'admin_app_scenario_category_list', methods: ['GET'])]
    public function index(ScenarioCategoryRepository $scenarioCategoryRepository): Response
    {
        return $this->render('Admin/scenario_category/list.html.twig', [
            'scenario_categories' => $scenarioCategoryRepository->findAll(),
        ]);
    }

    #[Route('/create', name: 'admin_app_scenario_category_create', methods: ['GET', 'POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $scenarioCategory = new ScenarioCategory();
        $form = $this->createForm(ScenarioCategoryType::class, $scenarioCategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($scenarioCategory);
            $entityManager->flush();
            $this->addFlash('success', 'La categorie '.$scenarioCategory.' a bien été créée.');

            return $this->redirectTo($request, $scenarioCategory->getId());
        }

        return $this->render('Admin/scenario_category/create.html.twig', [
            'scenario_category' => $scenarioCategory,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'admin_app_scenario_category_show', methods: ['GET'])]
    public function show(ScenarioCategory $scenarioCategory): Response
    {
        return $this->render('Admin/scenario_category/show.html.twig', [
            'scenario_category' => $scenarioCategory,
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_app_scenario_category_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ScenarioCategory $scenarioCategory, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ScenarioCategoryType::class, $scenarioCategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($scenarioCategory);
            $entityManager->flush();
            $this->addFlash('success', 'La categorie '.$scenarioCategory.' a bien été éditée.');

            return $this->redirectTo($request, $scenarioCategory->getId());
        }

        return $this->render('Admin/scenario_category/edit.html.twig', [
            'scenario_category' => $scenarioCategory,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'admin_app_scenario_category_delete', methods: ['POST'])]
    public function delete(Request $request, ScenarioCategory $scenarioCategory, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$scenarioCategory->getId(), $request->request->get('_token'))) {
            $entityManager->remove($scenarioCategory);
            $entityManager->flush();
            $this->addFlash('success', 'La categorie '.$scenarioCategory.' a bien été supprimée.');
        }

        return $this->redirectToRoute('admin_app_scenario_category_list', [], Response::HTTP_SEE_OTHER);
    }
}
