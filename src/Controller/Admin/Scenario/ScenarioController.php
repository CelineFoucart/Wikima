<?php

namespace App\Controller\Admin\Scenario;

use App\Entity\Episode;
use App\Entity\Scenario;
use App\Form\Admin\ScenarioType;
use App\Form\Admin\EpisodeShortType;
use App\Repository\ScenarioRepository;
use App\Form\Admin\ScenarioEpisodeType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use App\Controller\Admin\AbstractAdminController;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/scenario')]
#[IsGranted(new Expression("is_granted('ROLE_ADMIN') or is_granted('ROLE_EDITOR')"))]
class ScenarioController extends AbstractAdminController
{
    protected string $entityName = 'scenario';

    public function __construct(private EntityManagerInterface $entityManager, bool $enableScenario)
    {
        if (false === $enableScenario) {
            throw $this->createNotFoundException('Not Found');
        }
    }

    #[Route('/', name: 'admin_app_scenario_list', methods: ['GET'])]
    public function list(ScenarioRepository $scenarioRepository): Response
    {
        return $this->render('Admin/scenario/list.html.twig', [
            'scenarios' => $scenarioRepository->findForAdminList(false),
        ]);
    }

    #[Route('/archive', name: 'admin_app_scenario_archive_index', methods: ['GET'])]
    public function archiveIndexAction(ScenarioRepository $scenarioRepository): Response
    {
        return $this->render('Admin/scenario/archive.html.twig', [
            'scenarios' => $scenarioRepository->findForAdminList(true),
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
    public function episode(#[MapEntity(expr: 'repository.findById(id)')] Scenario $scenario, Request $request): Response
    {
        $episode = (new Episode())->setScenario($scenario)->setColor($scenario->getDefaultColor());

        foreach ($scenario->getPersons() as $person) {
            $episode->addPerson($person);
        }

        foreach ($scenario->getPlaces() as $place) {
            $episode->addPlace($place);
        }

        $form = $this->createForm(EpisodeShortType::class, $episode);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $episode->setCreatedAt(new \DateTimeImmutable());
            $lastSection = $scenario->getEpisodes()->last();
            if ($lastSection instanceof Episode) {
                $position = $lastSection->getPosition() + 1;
            } else {
                $position = 0;
            }
            $episode->setPosition($position);
            $this->entityManager->persist($episode);
            $this->entityManager->flush();
            $this->addFlash('success', "L'épisode ".$episode.' a bien été enregistré.');

            return $this->redirectToRoute('admin_app_scenario_episode', ['id' => $scenario->getId()]);
        }

        $formCollection = $this->createForm(ScenarioEpisodeType::class, $scenario);
        $formCollection->handleRequest($request);

        if ($formCollection->isSubmitted() && $formCollection->isValid()) {
            $this->entityManager->persist($scenario);
            $this->entityManager->flush();
            $this->addFlash('success', 'Les modifications ont bien été enregistrées.');

            return $this->redirectToRoute('admin_app_scenario_episode', ['id' => $scenario->getId()]);
        }

        return $this->render('Admin/scenario/episode.html.twig', [
            'scenario' => $scenario,
            'episode_active' => true,
            'form' => $form,
            'episode' => $episode,
            'formCollection' => $formCollection,
        ]);
    }

    #[Route('/{id}/archives', name: 'admin_app_scenario_episode_archives', methods: ['GET'])]
    public function episodeArchives(Scenario $scenario): Response
    {
        return $this->render('Admin/scenario/episode_archives.html.twig', [
            'scenario' => $scenario,
            'episode_active' => true,
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

    #[Route('/{id}/delete', name: 'admin_app_scenario_delete', methods: ['POST'])]
    public function delete(Request $request, Scenario $scenario): Response
    {
        if ($this->isCsrfTokenValid('delete'.$scenario->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($scenario);
            $this->entityManager->flush();
            $this->addFlash('success', 'Le scénario '.$scenario.' a bien été supprimé.');
        }

        return $this->redirectToRoute('admin_app_scenario_list', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/archive', name: 'admin_app_scenario_archive', methods: ['POST'])]
    public function archiveAction(Request $request, Scenario $scenario): Response
    {
        if ($this->isCsrfTokenValid('archive'.$scenario->getId(), $request->request->get('_token'))) {
            $isArchived = (bool) $scenario->isArchived();
            $message = $isArchived ? 'désarchivé' : 'archivé';
            $scenario->setArchived(!$isArchived);
            $this->entityManager->persist($scenario);
            $this->entityManager->flush();

            $this->addFlash('success', "Le scénario a été {$message} avec succès.");
        }

        return $this->redirectToRoute('admin_app_scenario_list', [], Response::HTTP_SEE_OTHER);
    }
}
