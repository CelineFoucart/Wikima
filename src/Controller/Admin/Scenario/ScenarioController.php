<?php

namespace App\Controller\Admin\Scenario;

use App\Entity\Episode;
use App\Entity\Scenario;
use App\Form\Admin\EpisodeType;
use App\Form\Admin\ScenarioType;
use App\Repository\ScenarioRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use App\Controller\Admin\AbstractAdminController;
use App\Repository\EpisodeRepository;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/scenario')]
#[IsGranted(new Expression("is_granted('ROLE_ADMIN') or is_granted('ROLE_EDITOR')"))]
class ScenarioController extends AbstractAdminController
{
    protected string $entityName = 'scenario';

    public function __construct(private EntityManagerInterface $entityManager)
    {
        
    }

    #[Route('/', name: 'admin_app_scenario_list', methods: ['GET'])]
    public function list(ScenarioRepository $scenarioRepository): Response
    {
        return $this->render('Admin/scenario/list.html.twig', [
            'scenarios' => $scenarioRepository->findAll(),
        ]);
    }

    #[Route('/create', name: 'admin_app_scenario_create', methods: ['GET', 'POST'])]
    public function create(Request $request): Response
    {
        $scenario = new Scenario();
        $form = $this->createForm(ScenarioType::class, $scenario);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $scenario->setCreatedAt(new \DateTimeImmutable());
            $this->entityManager->persist($scenario);
            $this->entityManager->flush();
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

    #[Route('/{id}/episodes', name: 'admin_app_scenario_episode', methods: ['GET', 'POST'])]
    public function episode(#[MapEntity(expr: 'repository.findById(id)')] Scenario $scenario, Request $request, EpisodeRepository $repository): Response
    {
        $episodeId = $request->query->getInt('episode', 0);
        $episode = null;

        if ($episodeId > 0) {
            $episode = $repository->findOneByScenario($episodeId, $scenario->getId());
        }

        if(null === $episode) {
            $episode = (new Episode())->setScenario($scenario);
        }
        
        $form = $this->createForm(EpisodeType::class, $episode);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) { 
            if (null === $episode->getCreatedAt()) {
                $episode->setCreatedAt(new \DateTimeImmutable());
                $lastSection = $scenario->getEpisodes()->last();
                if ($lastSection instanceof Episode) {
                    $position = $lastSection->getPosition() + 1;
                } else {
                    $position = 0;
                }
                $episode->setPosition($position);
            } else {
                $episode->setUpdatedAt(new \DateTime());
            }
            
            $this->entityManager->persist($episode);
            $this->entityManager->flush();
            $this->addFlash('success', "L'épisode ".$scenario.' a bien été créé.');

            return $this->redirectToRoute('admin_app_scenario_episode', ['id' => $scenario->getId()]);
        }

        return $this->render('Admin/scenario/episode.html.twig', [
            'scenario' => $scenario,
            'episode_active' => true,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_app_scenario_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Scenario $scenario): Response
    {
        $form = $this->createForm(ScenarioType::class, $scenario);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $scenario->setUpdatedAt(new \DateTime());
            $this->entityManager->persist($scenario);
            $this->entityManager->flush();
            $this->addFlash('success', 'Le scénario '.$scenario.' a bien été modifié.');

            return $this->redirectTo($request, $scenario->getId());
        }

        return $this->render('Admin/scenario/edit.html.twig', [
            'scenario' => $scenario,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'admin_app_scenario_delete', methods: ['POST'])]
    public function delete(Request $request, Scenario $scenario): Response
    {
        if ($this->isCsrfTokenValid('delete'.$scenario->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($scenario);
            $this->entityManager->flush();
            $this->addFlash('success', 'Le scénario '.$scenario.' a bien été supprimé.');
        }

        return $this->redirectToRoute('admin_app_scenario_list', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/delete-episode/{id}', name: 'admin_app_episode_delete', methods: ['POST'])]
    public function deleteEpisode(Request $request, Episode $episode): Response
    {
        $scenarioId = $episode->getScenario()->getId();

        if ($this->isCsrfTokenValid('delete'.$episode->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($episode);
            $this->entityManager->flush();
            $this->addFlash('success', "L'épisode a bien été supprimé.");
        }

        return $this->redirectToRoute('admin_app_scenario_episode', ['id' => $scenarioId], Response::HTTP_SEE_OTHER);
    }
}
