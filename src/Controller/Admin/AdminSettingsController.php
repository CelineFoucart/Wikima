<?php

namespace App\Controller\Admin;

use App\Service\ConfigService;
use App\Form\Admin\AdvancedSettingsType;
use App\Service\Modules\ModuleService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;

#[Route('/admin')]
class AdminSettingsController extends AbstractController
{
    #[Route('/settings', name: 'admin_app_settings')]
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

    #[Route('/modules', name: 'admin_app_modules')]
    #[Security("is_granted('ROLE_SUPER_ADMIN')")]
    public function modulesAction(Request $request, ModuleService $moduleService): Response
    {
        if ($request->isMethod('POST') && $this->isCsrfTokenValid('module_handler', $request->request->get('_token'))) {
            $status = $moduleService->handleSubmitData($request->request->all());

            if ($status) {
                $this->addFlash('success', "Les modifications ont bien été enregistrées.");
            } else {
                $this->addFlash('error', "L'opération a échoué. Vérifiez que le fichier le configuration .env.local n'a pas été supprimé.");
            }

            return $this->redirectToRoute('admin_app_modules');
        }

        return $this->render('Admin/modules.html.twig', [
            'modules' => $moduleService->getModules(),
        ]);
    }
}