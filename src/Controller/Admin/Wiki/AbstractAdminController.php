<?php

namespace App\Controller\Admin\Wiki;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

abstract class AbstractAdminController extends AbstractController
{
    protected string $entityName = "";

    protected function redirectTo(Request $request, int $id): RedirectResponse
    {
        if (null !== $request->get('btn_update_and_list')) {
            return $this->redirectToList();
        }
        if (null !== $request->get('btn_create_and_list')) {
            return $this->redirectToList();
        }

        if (null !== $request->get('btn_create_and_create')) {
            return $this->redirectToCreate();
        }

        if (null !== $request->get('btn_create_and_edit')) {
            return $this->redirectToEdit($id);
        }

        if (null !== $request->get('btn_update_and_edit')) {
            return $this->redirectToEdit($id);
        }

        return $this->redirectToList();
    }

    protected function redirectToList(): RedirectResponse
    {
        return $this->redirectToRoute("admin_app_". $this->entityName ."_list");
    }

    protected function redirectToCreate(): RedirectResponse
    {
        return $this->redirectToRoute("admin_app_". $this->entityName ."_create");
    }

    protected function redirectToEdit(int $id): RedirectResponse
    {
        return $this->redirectToRoute("admin_app_". $this->entityName ."_edit", ['id' => $id]);
    }
}