<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Data\SearchData;
use App\Entity\Image;
use App\Entity\Person;
use App\Form\AdvancedSearchType;
use App\Form\ImageType;
use App\Repository\ImageRepository;
use App\Repository\PersonRepository;
use App\Service\EditorService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class PersonAdminController extends CRUDController
{
    public function __construct(
        private PersonRepository $personRepository,
        private ImageRepository $imageRepository,
        private EditorService $editorService
    ) {
    }

    #[Security("is_granted('ROLE_ADMIN') or is_granted('ROLE_SUPER_ADMIN')")]
    public function imageAction(?int $id, Request $request): Response
    {
        $person = $this->personRepository->findById($id);

        if (!$person instanceof Person) {
            throw new NotFoundHttpException(sprintf('unable to find the object with id: %s', $id));
        }

        $page = $request->query->getInt('page', 1);
        $excludes = [];
        if ($person->getImage()) {
            $excludes[] = $person->getImage()->getId();
        }

        $image = (new Image())->setPortals($person->getPortals())->setCategories($person->getCategories());
        $form = $this->createForm(ImageType::class, $image);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->imageRepository->add($image, true);
            $person->setImage($image);
            $this->personRepository->add($person, true);

            $this->addFlash('success', "L'image a bien été ajoutée au personnage.");
            $uri = $request->server->get('REQUEST_URI');

            if ($uri) {
                return $this->redirect($uri);
            }

            return $this->redirectToRoute('admin_app_person_image', ['id' => $person->getId()]);
        }
        if ('POST' === $request->getMethod()) {
            $this->handleImage($request, $person);
            $uri = $request->server->get('REQUEST_URI');

            if ($uri) {
                return $this->redirect($uri);
            }

            return $this->redirectToRoute('admin_app_person_image', ['id' => $person->getId()]);
        }

        $searchData = (new SearchData())->setPage($page);
        $searchForm = $this->createForm(AdvancedSearchType::class, $searchData);
        $searchForm->handleRequest($request);

        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
            $images = $this->imageRepository->search($searchData, $excludes, 15);
        } else {
            $images = $this->imageRepository->findPaginated($page, $excludes, 15);
        }

        return $this->render('Admin/person/image_person.html.twig', [
            'person' => $person,
            'images' => $images,
            'form' => $form->createView(),
            'searchForm' => $searchForm->createView(),
        ]);
    }

    private function handleImage(Request $request, Person $person): void
    {
        $imageId = $request->request->getInt('imageId');
        $image = $this->imageRepository->find($imageId);

        if (null === $image) {
            $this->addFlash('error', "L'image que vous avez choisi n'existe pas.");
        }

        $delete = $request->request->get('delete');
        if ($this->isCsrfTokenValid('image'.$image->getId(), $request->request->get('_token'))) {
            if (null === $delete) {
                $person->setImage($image);
                $this->personRepository->add($person, true);
                $this->addFlash('success', "L'image a bien été ajoutée à l'article.");
            } else {
                $person->setImage(null);
                $this->personRepository->add($person, true);
                $this->addFlash('success', "L'image a bien été enlevée.");
            }
        }
    }
}
