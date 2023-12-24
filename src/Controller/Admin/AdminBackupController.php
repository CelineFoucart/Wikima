<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Backup;
use App\Repository\BackupRepository;
use App\Service\BackupService;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\ExpressionLanguage\Expression;

#[Route('/admin/backup')]
#[IsGranted(new Expression("is_granted('ROLE_SUPER_ADMIN')"))]
final class AdminBackupController extends AbstractAdminController
{
    public function __construct(
        private BackupService $backupService,
        private BackupRepository $backupRepository
    ) {
    }

    #[Route('/', name: 'admin_app_backup_list', methods:['GET'])]
    public function listAction(): Response
    {
        return $this->render('Admin/backup.html.twig', [
            'backups' => $this->backupRepository->findAll(),
        ]);
    }
    
    #[Route('/create', name: 'admin_app_backup_create', methods:['GET'])]
    public function createAction(Request $request): Response
    {
        $referer = $request->server->get('HTTP_REFERER', null);
        $filename = $this->backupService->save()->getFilename();

        if (null === $filename) {
            $this->addFlash('error', join('<br>', $this->backupService->getErrors()));
        } else {
            $backup = (new Backup())
                ->setFilename($filename)
                ->setCreatedAt($this->backupService->getDate())
            ;
            $this->backupRepository->add($backup);

            $this->addFlash( 'success', 'La sauvegarde de la base de donnée a été réalisée avec succès');
        }

        if ($referer !== null) {
            return $this->redirect($referer);
        }

        return $this->redirectToRoute('admin_app_backup_list');
    }

    #[Route('/{id}/download', name: 'admin_app_backup_download', methods:['GET'])]
    public function downloadAction(Backup $backup): Response
    {
        $folder = $this->backupService->getBackupFolder();
        $file = $folder.DIRECTORY_SEPARATOR.$backup->getFilename();

        $response = new BinaryFileResponse($file);
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $backup->getFilename()
        );

        return $response;
    }

    #[Route('/{id}/delete', name: 'admin_app_backup_delete', methods:['POST'])]
    public function deleteAction(Request $request, Backup $backup): Response
    {
        if ($this->isCsrfTokenValid('delete'.$backup->getId(), $request->request->get('_token'))) {
            $path = $this->backupService->getBackupFolder() . DIRECTORY_SEPARATOR . $backup->getFilename();

            if (file_exists($path)) {
                unlink($path);
            }

            $this->backupRepository->remove($backup, true);
            $this->addFlash('success', "La sauvegarde a été supprimée avec succès.");
        }

        return $this->redirectToRoute('admin_app_backup_list', [], Response::HTTP_SEE_OTHER);
    }
}
