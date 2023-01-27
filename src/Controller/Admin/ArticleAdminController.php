<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Article;
use App\Entity\Data\SearchData;
use App\Entity\Image;
use App\Entity\Section;
use App\Form\AdvancedSearchType;
use App\Form\ArticleType;
use App\Form\ImageType;
use App\Form\SectionType;
use App\Repository\ArticleRepository;
use App\Repository\ImageRepository;
use App\Repository\PortalRepository;
use App\Repository\SectionRepository;
use App\Security\Voter\VoterHelper;
use DateTime;
use DateTimeImmutable;
use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class ArticleAdminController extends CRUDController
{
    public function __construct(
        private SectionRepository $sectionRepository,
        private ArticleRepository $articleRepository,
        private ImageRepository $imageRepository,
        private PortalRepository $portalRepository
    ) {
    }

    public function sectionAction(?int $id, Request $request): Response
    {
        $article = $this->getArticle($id);
        $this->denyAccessUnlessGranted(VoterHelper::EDIT, $article);
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $article->setUpdatedAt(new DateTime());
            $this->articleRepository->add($article);
            $this->addFlash('success', "L'article a été mis à jour.");

            return $this->redirectToRoute('admin_app_article_section', ['id' => $article->getId()]);
        }

        $section = $this->getSection($request->query->getInt('section', 0), $article);
        $sectionForm = $this->createForm(SectionType::class, $section);
        $sectionForm->handleRequest($request);

        if ($sectionForm->isSubmitted() && $sectionForm->isValid()) {
            if (null === $section->getCreatedAt()) {
                $section->setCreatedAt(new DateTimeImmutable());
            } else {
                $section->setUpdatedAt(new DateTime());
            }
            $this->sectionRepository->add($section);
            $this->addFlash('success', 'Les modifications ont été enregistrées.');

            return $this->redirectToRoute('admin_app_article_section', ['id' => $article->getId()]);
        }

        return $this->renderForm('Admin/article/section.html.twig', [
            'article' => $article,
            'sectionForm' => $sectionForm,
            'form' => $form,
        ]);
    }

    public function galleryAction(?int $id, Request $request): Response
    {
        $article = $this->getArticle($id);
        $this->denyAccessUnlessGranted(VoterHelper::EDIT, $article);
        $page = $request->query->getInt('page', 1);

        $image = (new Image())->setPortals($article->getPortals());
        $formImage = $this->createForm(ImageType::class, $image, ['allow_extra_fields' => true]);
        $formImage->handleRequest($request);

        if ($formImage->isSubmitted() && $formImage->isValid()) {
            $this->imageRepository->add($image, true);
            $article->addImage($image);
            $this->imageRepository->add($image, true);
            $this->articleRepository->add($article, true);
            $this->addFlash('success', "L'image a bien été ajoutée.");

            return $this->redirectToRoute('admin_app_article_gallery', ['id' => $article->getId()]);
        }

        if ('POST' === $request->getMethod()) {
            $this->handleGallery($request, $article);

            return $this->redirectToRoute('admin_app_article_gallery', ['id' => $article->getId()]);
        }

        $excludes = array_map(function (Image $item) {
            return $item->getId();
        }, $article->getImages()->toArray());
        $searchData = (new SearchData())->setPage($page);
        $form = $this->createForm(AdvancedSearchType::class, $searchData);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $images = $this->imageRepository->search($searchData, $excludes, 15);
        } else {
            $images = $this->imageRepository->findPaginated($page, $excludes, 15);
        }

        return $this->render('Admin/article/gallery.html.twig', [
            'article' => $article,
            'images' => $images,
            'form' => $form->createView(),
            'formImage' => $formImage->createView(),
        ]);
    }

    protected function preCreate(Request $request, object $object): ?Response
    {
        $portalId = $request->query->getInt('portal');
        if (0 === $portalId) {
            return null;
        }

        $portal = $this->portalRepository->find($portalId);
        if (!$portal) {
            return null;
        }

        $object->addPortal($portal);

        return null;
    }

    private function getSection(int $sectionId, $article): Section
    {
        if (0 === $sectionId) {
            return (new Section())->setArticle($article);
        }

        $section = $this->sectionRepository->findOneByArticle($sectionId, $article->getId());

        if (!$section) {
            $section = (new Section())->setArticle($article);
        }

        return $section;
    }

    /**
     * Retrieve the article.
     */
    private function getArticle(int $id): Article
    {
        $article = $this->articleRepository->findById($id);

        if (!$article instanceof Article) {
            throw new NotFoundHttpException(sprintf('unable to find the object with id: %s', $id));
        }

        return $article;
    }

    /**
     * Update the article gallery, adding or removing an image.
     */
    private function handleGallery(Request $request, Article $article): void
    {
        $imageId = $request->request->getInt('imageId');
        $image = $this->imageRepository->find($imageId);

        if (null === $image) {
            $this->addFlash('error', "L'image que vous avez choisi n'existe pas.");
        }

        $delete = $request->request->get('delete');

        if ($this->isCsrfTokenValid('image'.$image->getId(), $request->request->get('_token'))) {
            if (null === $delete) {
                $article->addImage($image);
                $this->articleRepository->add($article);
                $this->addFlash('success', "L'image a bien été ajoutée à l'article.");
            } else {
                $article->removeImage($image);
                $this->articleRepository->add($article);
                $this->addFlash('success', "L'image a bien été enlevée de l'article.");
            }
        }
    }

    /**
     * Redirect the user to section action if this choice is edit.
     */
    protected function redirectTo(Request $request, object $object): RedirectResponse
    {
        if (null !== $request->get('btn_create_and_edit')) {
            return $this->redirectToRoute('admin_app_article_section', ['id' => $object->getId()]);
        }

        return parent::redirectTo($request, $object);
    }
}
