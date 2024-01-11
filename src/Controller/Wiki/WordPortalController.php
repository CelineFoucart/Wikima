<?php

declare(strict_types=1);

namespace App\Controller\Wiki;

use App\Entity\Portal;
use App\Service\LogService;
use App\Service\Word\WordPortalGenerator;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/export/portal/{slug}')]
final class WordPortalController extends AbstractController
{
    public function __construct(private LogService $logService, private WordPortalGenerator $generator)
    {
    }

    #[Route('/articles', name: 'app_portal_word_article')]
    public function articleAction(#[MapEntity(expr: 'repository.findBySlug(slug)')] Portal $portal): Response
    {
        return $this->returnFileAsResponse($portal, 'article');
    }

    #[Route('/timelines', name: 'app_portal_word_timeline')]
    public function timelineAction(#[MapEntity(expr: 'repository.findBySlug(slug)')] Portal $portal): Response
    {
        return $this->returnFileAsResponse($portal, 'timeline');
    }

    #[Route('/persons', name: 'app_portal_word_person')]
    public function personAction(#[MapEntity(expr: 'repository.findBySlug(slug)')] Portal $portal): Response
    {
        return $this->returnFileAsResponse($portal, 'person');
    }

    #[Route('/place', name: 'app_portal_word_place')]
    public function placeAction(#[MapEntity(expr: 'repository.findBySlug(slug)')] Portal $portal): Response
    {
        return $this->returnFileAsResponse($portal, 'place');
    }

    private function returnFileAsResponse(Portal $portal, string $type): Response
    {
        try {
            $file = $this->generator->setPortal($portal)->generate($type);
            $response = new BinaryFileResponse($file['path']);
            $response->setContentDisposition(
                ResponseHeaderBag::DISPOSITION_ATTACHMENT,
                $file['filename']
            );
            $response->deleteFileAfterSend();

            return $response;
        } catch (\Exception $th) {
            $this->addFlash('error', "Le fichier n'a pas pu être généré, car il y a des liens vers des images invalides ou un code HTML invalide.");
            $this->logService->error("Génération de '{$type}-{$portal->getSlug()}.docx'", $th->getMessage(), 'Portail');

            return $this->redirectToRoute('app_portal_show', ['slug' => $portal->getSlug()]);
        }
    }
}
