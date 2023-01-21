<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Backup;
use App\Repository\BackupRepository;
use App\Service\BackupService;
use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

final class BackupAdminController extends CRUDController
{
    public function __construct(
        private BackupService $backupService,
        private BackupRepository $backupRepository
    ) {
    }

    /**
     * @throws AccessDeniedException If access is not granted
     */
    public function createAction(Request $request): Response
    {
        $this->admin->checkAccess('create');

        $filename = $this->backupService->save()->getFilename();

        if (null === $filename) {
            $this->addFlash(
                'sonata_flash_error',
                join('<br>', $this->backupService->getErrors())
            );
        } else {
            $backup = (new Backup())
                ->setFilename($filename)
                ->setCreatedAt($this->backupService->getDate())
            ;
            $this->backupRepository->add($backup);

            $this->addFlash(
                'sonata_flash_success',
                'La sauvegarde de la base de donnée a été réalisée avec succès'
            );
        }

        return $this->redirectToList();
    }

    public function downloadAction(int $id): Response
    {
        $backup = $this->backupRepository->find($id);

        if (null === $backup) {
            throw $this->createNotFoundException("Ce fichier n'existe pas.");
        }

        $folder = $this->backupService->getBackupFolder();
        $file = $folder.DIRECTORY_SEPARATOR.$backup->getFilename();

        $response = new BinaryFileResponse($file);
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $backup->getFilename()
        );

        return $response;
    }
}
