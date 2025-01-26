<?php

declare(strict_types=1);

namespace App\Controller\Admin\Image;

use App\Controller\Admin\AbstractAdminController;
use App\Entity\Image;
use App\Form\Admin\ImageType;
use App\Repository\CategoryRepository;
use App\Repository\ImageRepository;
use App\Repository\PortalRepository;
use App\Service\ImageResizeHelper;
use Doctrine\ORM\EntityManagerInterface;
use League\Glide\Server;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/image')]
#[IsGranted(new Expression("is_granted('ROLE_ADMIN') or is_granted('ROLE_EDITOR')"))]
final class AdminImageController extends AbstractAdminController
{
    protected string $entityName = 'image';

    public function __construct(
        private ImageRepository $imageRepository
    ) {
    }

    #[Route('/', name: 'admin_app_image_list', methods: ['GET'])]
    public function listAction(): Response
    {
        return $this->render('Admin/image/list.html.twig');
    }

    #[Route('/{id}', name: 'admin_app_image_edit', methods: ['GET', 'POST'])]
    public function editAction(Request $request, Image $image): Response
    {
        $form = $this->createForm(ImageType::class, $image);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $image->setUpdatedAt(new \DateTime());
            $this->imageRepository->add($image, true);
            $this->addFlash('success', "Les informations de l'image ".$image->getTitle().' ont bien été modifiées.');

            return $this->redirectToRoute('admin_app_image_edit', ['id' => $image->getId()]);
        }

        return $this->render('Admin/image/edit.html.twig', [
            'form' => $form->createView(),
            'image' => $image,
        ]);
    }
}
