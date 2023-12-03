<?php

namespace App\Controller;

use App\Entity\Scenario;
use App\Entity\Data\SearchData;
use App\Form\Search\SearchPortalType;
use App\Repository\ScenarioRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/scenario')]
class ScenarioController extends AbstractController
{
    public function __construct(bool $enableScenario)
    {
        if (false === $enableScenario) {
            throw $this->createNotFoundException('Not Found');
        }
    }

    #[Route('', name: 'app_scenario_index')]
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

    #[Route('/{slug}', name: 'app_scenario_show')]
    public function indexShow(Scenario $scenario): Response
    {
        if ($scenario->isPublic() !== true && !$this->isGranted('ROLE_EDITOR')) {
            throw $this->createAccessDeniedException();
        }

        return $this->render('scenario/show.html.twig', [
            'scenario' => $scenario,
        ]);
    }
}