<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Repository\PortalRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpFoundation\RedirectResponse;

class NoteAdminController extends CRUDController
{
    public function __construct(
        private EntityManagerInterface $em, 
        private PortalRepository $portalRepository,
        private CategoryRepository $categoryRepository
    ) {
    }

    protected function preCreate(Request $request, object $object): ?Response
    {
        $portalId = $request->query->getInt('portal');
        if (0 !== $portalId) {
            $portal = $this->portalRepository->find($portalId);
            if ($portal) {
                $object->setPortal($portal);
            }
        }

        $categoryId = $request->query->getInt('category');
        if (0 !== $categoryId) {
            $category = $this->categoryRepository->find($categoryId);
            if ($category) {
                $object->setCategory($category);
            }
        }

        return null;
    }

    /**
     * Redirect the user to section action if this choice is edit.
     */
    protected function redirectTo(Request $request, object $object): RedirectResponse
    {
        if (null !== $request->get('btn_create_and_edit') || null !== $request->get('btn_update_and_edit')) {
            return $this->redirectToRoute('admin_app_note_show', ['id' => $object->getId()]);
        }

        return parent::redirectTo($request, $object);
    }

}