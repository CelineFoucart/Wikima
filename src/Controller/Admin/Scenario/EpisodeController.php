<?php

namespace App\Controller\Admin\Scenario;

use App\Entity\Episode;
use App\Entity\Scenario;
use App\Form\Admin\EpisodeType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/scenario-episode')]
#[IsGranted(new Expression("is_granted('ROLE_ADMIN') or is_granted('ROLE_EDITOR')"))]
class EpisodeController extends AbstractController
{
    public function __construct(private EntityManagerInterface $entityManager, bool $enableScenario)
    {
        if (false === $enableScenario) {
            throw $this->createNotFoundException('Not Found');
        }
    }

    #[Route('/{id}/create', name: 'admin_app_episode_create', methods: ['GET', 'POST'])]
    public function create(Scenario $scenario, Request $request): Response
    {
        $episode = (new Episode())->setScenario($scenario);
        $form = $this->createForm(EpisodeType::class, $episode);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $episode->setCreatedAt(new \DateTimeImmutable());
            $lastSection = $scenario->getEpisodes()->last();
            $position = ($lastSection instanceof Episode) ? ($lastSection->getPosition() + 1) : 0;
            $episode->setPosition($position);
            $this->entityManager->persist($episode);
            $this->entityManager->flush();
            $this->addFlash('success', "L'épisode ".$episode.' a bien été créé.');

            return $this->redirectToRoute('admin_app_scenario_episode', ['id' => $scenario->getId()]);
        }

        return $this->render('Admin/scenario/episode/edit.html.twig', [
            'scenario' => $scenario,
            'form' => $form,
            'episode' => $episode,
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_app_episode_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Episode $episode): Response
    {
        $form = $this->createForm(EpisodeType::class, $episode);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) { 
            $episode->setUpdatedAt(new \DateTime());
            $this->entityManager->persist($episode);
            $this->entityManager->flush();
            $this->addFlash('success', "L'épisode ".$episode.' a bien été modifié.');

            return $this->redirectToRoute('admin_app_episode_edit', ['id' => $episode->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('Admin/scenario/episode/edit.html.twig', [
            'scenario' => $episode->getScenario(),
            'form' => $form,
            'episode' => $episode,
        ]);
    }

    #[Route('/{id}/delete', name: 'admin_app_episode_delete', methods: ['POST'])]
    public function delete(Request $request, Episode $episode): Response
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