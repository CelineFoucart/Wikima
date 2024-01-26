<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\InstallationType;
use App\Service\ConfigService;
use App\Service\InstallationService;
use App\Form\User\RegistrationFormType;
use App\Form\Admin\AdvancedSettingsType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class InstallationController extends AbstractController
{
    public function __construct(
        private InstallationService $installationService
    ) {

        if ($this->installationService->isInstalled()) {
            throw $this->createNotFoundException('Not Found');
        }

    }

    #[Route('/installation', name: 'app_installation_database')]
    public function index(Request $request): Response
    {
        if (!$this->installationService->hasEnvVarFile()) {
            $this->addFlash('error', "Il n'y a pas de fichier de configuration. Il sera créé.");
        }

        $data = [
            'DB_NAME' => '', 'DB_USER' => '', 'DB_PASSWORD' => '', 'DB_HOST' => 'localhost', 'DB_PORT' => 3306, 'serverVersion' => 'mariadb-10.4.11'
        ];
        
        $form = $this->createForm(InstallationType::class, $data);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) { 
            $data = $form->getData();
            $this->installationService->setDatabaseURL($data);
            
            return $this->redirectToRoute('app_installation_database_exec');
        }

            return $this->render('installation/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/installation/database', name: 'app_installation_database_exec')]
    public function database(KernelInterface $kernel): Response
    {
        $this->installationService->execMigrations($kernel);

        return $this->redirectToRoute('app_installation_account');
    }

    #[Route('/installation/account', name: 'app_installation_account')]
    public function account(Request $request): Response
    {
        if (!$this->installationService->hasEnvVarFile()) {
            $this->addFlash('error', "Il n'y a pas de fichier de configuration. Il sera créé.");

            return $this->redirectToRoute('app_installation_database');
        }

        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) { 
            $this->installationService->setAdmin($user, $form->get('plainPassword')->getData());

            return $this->redirectToRoute('app_installation_settings');
        }

        return $this->render('installation/user.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/installation/settings', name: 'app_installation_settings')]
    public function settings(Request $request, ConfigService $configService): Response
    {
        $envVars = $configService->getEnvVars();

        $form = $this->createForm(AdvancedSettingsType::class, $envVars);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) { 
            $data = $form->getData();

            if ($data['faviconFile']) {
                $configService->move($data['faviconFile'], $envVars['WIKI_FAVICON']);
            }

            if ($data['bannerFile'] && $data['bannerFile'] ) {
                /** @var UploadedFile */
                $file = $data['bannerFile'];
                $name = "banner.".$file->getClientOriginalExtension();
                $statusBanner = $configService->move($data['bannerFile'], $name);

                if ($statusBanner) {
                    $data['WIKI_BANNER'] = $name;
                }
            }

            $configService->setAppendTo(true)->save($data);
            $this->installationService->endInstallation();

            return $this->redirectToRoute('app_home');
        }

        return $this->render('installation/settings.html.twig', [
            'form' => $form->createView(),
            'bannerFile' => null,
            'faviconFile' => null,
        ]);
    }
}
