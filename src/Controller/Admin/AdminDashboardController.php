<?php

namespace App\Controller\Admin;

use App\Entity\About;
use App\Form\AboutType;
use App\Repository\NoteRepository;
use App\Repository\AboutRepository;
use App\Repository\PortalRepository;
use App\Service\Statistics\DatabaseSize;
use App\Service\Statistics\SatisticsEntity;
use App\Service\Statistics\StatisticsHandler;
use DateTime;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\ExpressionLanguage\Expression;

class AdminDashboardController extends AbstractController
{
    #[Route('/admin', name: 'admin_app_dashboard')]
    #[IsGranted(new Expression("is_granted('ROLE_ADMIN') or is_granted('ROLE_SUPER_ADMIN') or is_granted('ROLE_EDITOR')"))]
    public function dashboardAction(StatisticsHandler $statisticsHandler, NoteRepository $noteRepository, DatabaseSize $databaseSize, PortalRepository $portalRepository, Request $request): Response
    {
        $tables = ['category', 'portal', 'article', 'image', 'place', 'person', 'page', 'note', 'comment', 'user', 'idiom', 'timeline'];

        foreach ($tables as $table) {
            $statisticsHandler->addEntity(new SatisticsEntity($table));
        }

        $stats = $statisticsHandler->getStatistics();
        $total = (int)$stats['article'] + (int)$stats['person'] + (int) $stats['place'] + (int) $stats['timeline'] + (int) $stats['image'];

        $year = $request->query->get('year', null);

        if (null === $year) {
            $year = (new DateTime())->format('Y');
        }

        $articleByMonthData = $statisticsHandler->getStatsByMonth('article', 'created_at', $year, 'is_draft = 0 or is_draft IS NULL');

        $articleByMonth = [];
        for ($i=0; $i < 12; $i++) { 
            $articleByMonth[] = 0;
        }

        $totalThisYear = 0;

        foreach ($articleByMonthData as $month) {
            $totalArticle = (int)$month['total'];
            $articleByMonth[(int)$month['monthId'] - 1] = $totalArticle;
            $totalThisYear += $totalArticle;
        }
        
        return $this->render('Admin/dashboard.html.twig', [
            'stats' => $stats,
            'total' => $total,
            'notes' => $noteRepository->findLastNotes(5),
            'size'  => $databaseSize->getSize(),
            'articleByMonth' => $articleByMonth,
            'totalThisYear' => $totalThisYear,
            'year' => $year,
            'portalStats' => $portalRepository->getPortalStats(),
        ]);
    }

    #[Route('/admin/about', name: 'admin_app_overview')]
    #[IsGranted(new Expression("is_granted('ROLE_ADMIN') or is_granted('ROLE_SUPER_ADMIN')"))]
    public function overviewAction(Request $request, AboutRepository $aboutRepository): Response
    {
        $overview = $aboutRepository->findAboutRow('overview');
        if (null === $overview) {
            $overview = (new About())->setType('overview');
        }

        $form = $this->createForm(AboutType::class, $overview);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $aboutRepository->add($overview);
            $this->addFlash('success', 'Les modifications ont été enregistrées.');

            return $this->redirectToRoute('admin_app_overview');
        }

        return $this->renderForm('Admin/overview.html.twig', [
            'form' => $form,
        ]);
    }
}