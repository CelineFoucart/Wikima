<?php

namespace App\Controller\Admin\Scenario;

use App\Entity\Scenario;
use App\Form\Admin\ScenarioType;
use App\Repository\ScenarioRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Controller\Admin\AbstractAdminController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/scenario')]
class ScenarioController extends AbstractAdminController
{
    protected string $entityName = 'scenario';

    #[Route('/', name: 'admin_app_scenario_list', methods: ['GET'])]
    public function list(ScenarioRepository $scenarioRepository): Response
    {
        return $this->render('Admin/scenario/list.html.twig', [
            'scenarios' => $scenarioRepository->findAll(),
        ]);
    }

    #[Route('/create', name: 'admin_app_scenario_create', methods: ['GET', 'POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $scenario = new Scenario();
        $form = $this->createForm(ScenarioType::class, $scenario);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $scenario->setCreatedAt(new \DateTimeImmutable());
            $entityManager->persist($scenario);
            $entityManager->flush();
            $this->addFlash('success', 'Le scénario '.$scenario.' a bien été créé.');

            return $this->redirectTo($request, $scenario->getId());
        }

        return $this->render('Admin/scenario/create.html.twig', [
            'scenario' => $scenario,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'admin_app_scenario_show', methods: ['GET'])]
    public function show(Scenario $scenario): Response
    {
        return $this->render('Admin/scenario/show.html.twig', [
            'scenario' => $scenario,
            'general_active' => true,
        ]);
    }

    #[Route('/{id}/episodes', name: 'admin_app_scenario_episode', methods: ['GET'])]
    public function episode(Scenario $scenario): Response
    {
        return $this->render('Admin/scenario/episode.html.twig', [
            'scenario' => $scenario,
            'episode_active' => true,
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_app_scenario_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Scenario $scenario, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ScenarioType::class, $scenario);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $scenario->setUpdatedAt(new \DateTime());
            $entityManager->persist($scenario);
            $entityManager->flush();
            $this->addFlash('success', 'Le scénario '.$scenario.' a bien été modifié.');

            return $this->redirectTo($request, $scenario->getId());
        }

        return $this->render('Admin/scenario/edit.html.twig', [
            'scenario' => $scenario,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'admin_app_scenario_delete', methods: ['POST'])]
    public function delete(Request $request, Scenario $scenario, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$scenario->getId(), $request->request->get('_token'))) {
            $entityManager->remove($scenario);
            $entityManager->flush();
            $this->addFlash('success', 'Le scénario '.$scenario.' a bien été supprimé.');
        }

        return $this->redirectToRoute('admin_app_scenario_list', [], Response::HTTP_SEE_OTHER);
    }
}
