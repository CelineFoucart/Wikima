<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Log;
use App\Repository\LogRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/logs')]
#[IsGranted(new Expression("is_granted('ROLE_ADMIN') or is_granted('ROLE_SUPER_ADMIN')"))]
final class LogController extends AbstractController
{
    #[Route('/', name: 'admin_app_log_list', methods:['GET'])]
    public function listAction(): Response
    {
        return $this->render('Admin/log/list.html.twig');
    }

    #[Route('/{id}', name: 'admin_app_log_show', methods:['GET'])]
    public function showAction(Log $log): Response
    {
        return $this->render('Admin/log/show.html.twig', [
            'log' => $log,
        ]);
    }

    #[Route('/{id}/delete', name: 'admin_app_log_delete', methods:['POST'])]
    public function deleteAction(Request $request, Log $log, LogRepository $logRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$log->getId(), $request->request->get('_token'))) {
            $logRepository->remove($log, true);
            $this->addFlash('success', "Le log a été supprimé avec succès.");
        }

        return $this->redirectToRoute('admin_app_log_list', [], Response::HTTP_SEE_OTHER);
    }
}