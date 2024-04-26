<?php

namespace App\Controller;

use App\Entity\Data\SearchData;
use App\Entity\Scenario;
use App\Form\Search\SearchPortalType;
use App\Form\Search\SearchType;
use App\Repository\ScenarioRepository;
use App\Service\Word\WordScenarioGenerator;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/scenario')]
class ScenarioController extends AbstractController
{
    public function __construct(bool $enableScenario)
    {
        if (false === $enableScenario) {
            throw $this->createNotFoundException('Not Found');
        }
    }

    #[Route('', name: 'app_scenario_index', methods: ['GET'])]
    public function indexAction(ScenarioRepository $scenarioRepository, int $perPageEven, Request $request): Response
    {
        $withPrivate = $this->isGranted('ROLE_EDITOR');
        $search = (new SearchData())->setPage($request->query->getInt('page', 1));
        $form = $this->createForm(SearchPortalType::class, $search, ['allow_extra_fields' => true]);
        $form->handleRequest($request);

        return $this->render('scenario/index.html.twig', [
            'scenarios' => $scenarioRepository->findPaginatedByName($search, $perPageEven, $withPrivate),
            'form' => $form,
        ]);
    }

    #[Route('/{slug}', name: 'app_scenario_show', methods: ['GET'])]
    public function showAction(#[MapEntity(expr: 'repository.findBySlug(slug)')] Scenario $scenario): Response
    {
        if (true !== $scenario->isPublic() && !$this->isGranted('ROLE_EDITOR')) {
            throw $this->createAccessDeniedException();
        } elseif (true === $scenario->isArchived() && !$this->isGranted('ROLE_EDITOR')) {
            throw $this->createNotFoundException();
        }

        return $this->render('scenario/show.html.twig', [
            'scenario' => $scenario,
            'form' => $this->createForm(SearchType::class, new SearchData())->createView(),
        ]);
    }

    #[Route('/{slug}/word', name: 'app_scenario_word')]
    public function wordAction(Scenario $scenario, WordScenarioGenerator $generator): Response
    {
        if (true !== $scenario->isPublic() && !$this->isGranted('ROLE_EDITOR')) {
            throw $this->createAccessDeniedException();
        }

        $file = $generator->setScenario($scenario)->generate();

        $response = new BinaryFileResponse($file['path']);
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $file['filename']
        );
        $response->deleteFileAfterSend();

        return $response;
    }
}
