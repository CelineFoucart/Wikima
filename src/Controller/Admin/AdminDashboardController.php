<?php

namespace App\Controller\Admin;

use App\Entity\About;
use App\Form\AboutType;
use App\Service\ConfigService;
use App\Form\Admin\AdvancedSettingsType;
use App\Repository\NoteRepository;
use App\Repository\AboutRepository;
use App\Service\Statistics\SatisticsEntity;
use App\Service\Statistics\StatisticsHandler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class AdminDashboardController extends AbstractController
{
    #[Route('/admin', name: 'admin_app_dashboard')]
    #[Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_SUPER_ADMIN') or is_granted('ROLE_EDITOR')")]
    public function dashboardAction(StatisticsHandler $statisticsHandler, NoteRepository $noteRepository): Response
    {
        $tables = ['category', 'portal', 'article', 'image', 'place', 'person', 'page', 'note', 'comment', 'user'];

        foreach ($tables as $table) {
            $statisticsHandler->addEntity(new SatisticsEntity($table));
        }

        $stats = $statisticsHandler->getStatistics();
        $total = (int)$stats['article'] + (int)$stats['person'] + (int) $stats['place'];
        
        return $this->render('Admin/dashboard.html.twig', [
            'stats' => $stats,
            'total' => $total,
            'notes' => $noteRepository->findLastNotes(5),
        ]);
    }

    #[Route('/admin/about', name: 'admin_app_overview')]
    #[Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_SUPER_ADMIN')")]
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

    #[Route('/admin/settings', name: 'admin_app_settings')]
    #[Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_SUPER_ADMIN')")]
    public function settingsAction(Request $request, ConfigService $configService): Response
    {
        $envVars = $configService->getEnvVars();
        $hasEnvFile = $configService->hasConfigFile();
        $form = $this->createForm(AdvancedSettingsType::class, $envVars);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) { 
            $data = $form->getData();

            if ($data['faviconFile']) {
                $statusFavicon = $configService->move($data['faviconFile'], $envVars['WIKI_FAVICON']);

                if (!$statusFavicon) {
                    $this->addFlash('error', "Le chargement du nouveau favicon a échoué.");
                }
            }

            if ($data['bannerFile'] && $data['bannerFile'] ) {
                /** @var UploadedFile */
                $file = $data['bannerFile'];
                $name = "banner.".$file->getClientOriginalExtension();
                $statusBanner = $configService->move($data['bannerFile'], $name);

                if (!$statusBanner) {
                    $this->addFlash('error', "Le chargement de la nouvelle bannière a échoué.");
                }  else {
                    $data['WIKI_BANNER'] = $name;
                }
            }

            $status = $configService->save($data);

            if ($status) {
                $this->addFlash('success', "Les paramètres ont bien été enregistrés.");
            } else {
                $this->addFlash('error', "La sauvegarde a échoué, car le fichier de configuration .env.local est manquant.");
            }

            return $this->redirectToRoute('admin_app_settings');
        }

        $imgPath = $configService->getPublicDir() . DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR;

        $faviconFilePath = $imgPath . $envVars['WIKI_FAVICON'];
        $bannerFilePath = $imgPath . $envVars['WIKI_FAVICON'];
        
        return $this->render('Admin/settings.html.twig', [
            'hasEnvFile' => $hasEnvFile,
            'form' => $form->createView(),
            'envVars' => $envVars,
            'faviconFile' => file_exists($faviconFilePath) ? $envVars['WIKI_FAVICON'] : null,
            'bannerFile' => file_exists($bannerFilePath) ? $envVars['WIKI_BANNER'] : null,
        ]);
    }
}