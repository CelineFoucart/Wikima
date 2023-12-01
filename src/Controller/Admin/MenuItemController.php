<?php

namespace App\Controller\Admin;

use App\Entity\MenuItem;
use App\Form\Admin\MenuItemType;
use App\Repository\MenuItemRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\ExpressionLanguage\Expression;

#[Route('/admin/menu')]
#[IsGranted(new Expression("is_granted('ROLE_ADMIN') or is_granted('ROLE_SUPER_ADMIN')"))]
class MenuItemController extends AbstractAdminController
{
    protected string $entityName = "menu_item";

    private const MAX_LENGTH = 15; 

    public function __construct(private MenuItemRepository $menuItemRepository)
    {
        
    }

    #[Route('/', name: 'admin_app_menu_item_list', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('Admin/menu_item/index.html.twig', [
            'menu_items' => $this->menuItemRepository->findBy([], ['position' => 'ASC']),
            'total' => $this->menuItemRepository->count([]),
            'limit' => self::MAX_LENGTH,
        ]);
    }

    #[Route('/new', name: 'admin_app_menu_item_create', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $menuItem = new MenuItem();
        $form = $this->createForm(MenuItemType::class, $menuItem);
        $form->handleRequest($request);

        $total = $this->menuItemRepository->count([]);

        if ($form->isSubmitted() && $form->isValid() && $total <= self::MAX_LENGTH) {
            $position = $this->menuItemRepository->findMaxPosition();
            $menuItem->setPosition($position);
            $entityManager->persist($menuItem);
            $entityManager->flush();
            $this->addFlash('success', "L'élement de menu a bien été créé.");

            return $this->redirectTo($request, $menuItem->getId());
        }

        return $this->render('Admin/menu_item/new.html.twig', [
            'menu_item' => $menuItem,
            'form' => $form,
            'total' => $total,
            'limit' => self::MAX_LENGTH,
        ]);
    }

    #[Route('/{id}', name: 'admin_app_menu_item_show', methods: ['GET'])]
    public function show(MenuItem $menuItem): Response
    {
        return $this->render('Admin/menu_item/show.html.twig', [
            'menu_item' => $menuItem,
            'total' => $this->menuItemRepository->count([]),
            'limit' => self::MAX_LENGTH,
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_app_menu_item_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, MenuItem $menuItem, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(MenuItemType::class, $menuItem);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($menuItem);
            $entityManager->flush();
            $this->addFlash('success', "L'élement de menu a bien été modifié.");

            return $this->redirectTo($request, $menuItem->getId());
        }

        return $this->render('Admin/menu_item/edit.html.twig', [
            'menu_item' => $menuItem,
            'form' => $form,
            'total' => $this->menuItemRepository->count([]),
            'limit' => self::MAX_LENGTH,
        ]);
    }

    #[Route('/{id}', name: 'admin_app_menu_item_delete', methods: ['POST'])]
    public function delete(Request $request, MenuItem $menuItem, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$menuItem->getId(), $request->request->get('_token'))) {
            $entityManager->remove($menuItem);
            $entityManager->flush();
            $this->addFlash('success', "L'élement de menu a bien été supprimé.");
        } else {
            $this->addFlash('error', "Le token CSRF est invalide.");
        }

        return $this->redirectToRoute('admin_app_menu_item_index', [], Response::HTTP_SEE_OTHER);
    }
}
