<?php

namespace App\Controller\Admin\Idiom;

use App\Entity\Idiom;
use App\Entity\Image;
use DateTimeImmutable;
use App\Entity\IdiomArticle;
use App\Form\Admin\ImageType;
use App\Form\Admin\IdiomArticleType;
use App\Entity\Data\SearchData;
use App\Form\Search\AdvancedSearchType;
use App\Repository\IdiomArticleRepository;
use App\Repository\ImageRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\ExpressionLanguage\Expression;

#[Route('/admin/idiom')]
#[IsGranted(new Expression("is_granted('ROLE_ADMIN') or is_granted('ROLE_EDITOR')"))]
class AdminIdiomArticleController extends AbstractController
{
    public function __construct(
        private IdiomArticleRepository $idiomArticleRepository,
        bool $enableIdiom
    ) {
        if (false === $enableIdiom) {
            throw $this->createNotFoundException('Not Found');
        }
    }

    #[Route('/{id}/articles/create', name: 'admin_app_idiom_article_create', methods: ['GET', 'POST'])]
    public function createAction(Idiom $idiom, Request $request): Response
    {
        $article = (new IdiomArticle())->setIdiom($idiom);
        $form = $this->createForm(IdiomArticleType::class, $article);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) { 
            $article->setCreatedAt(new DateTimeImmutable());
            $this->idiomArticleRepository->add($article, true);
            $this->addFlash('success', "L'article a été ajouté.");

            return $this->redirectAction($article, $request);
        }

        return $this->render('Admin/idiom_article/create.html.twig', [
            'idiom' => $idiom,
            'section_active' => true,
            'form' => $form->createView(),
            'article' => $article,
        ]);
    }

    #[Route('/articles/{id}/edit', name: 'admin_app_idiom_article_edit', methods: ['GET', 'POST'])]
    public function editAction(IdiomArticle $article, Request $request): Response
    {
        $form = $this->createForm(IdiomArticleType::class, $article);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) { 
            $article->setUpdatedAt(new \DateTime());
            $this->idiomArticleRepository->add($article, true);
            $this->addFlash('success', "L'article a été modifié.");

            return $this->redirectAction($article, $request);
        }

        return $this->render('Admin/idiom_article/create.html.twig', [
            'idiom' => $article->getIdiom(),
            'section_active' => true,
            'form' => $form->createView(),
            'article' => $article,
        ]);
    }

    #[Route('/articles/{id}/gallery', name: 'admin_app_idiom_article_gallery', methods: ['GET', 'POST'])]
    public function galleryAction(IdiomArticle $article, Request $request, ImageRepository $imageRepository): Response
    {
        $page = $request->query->getInt('page', 1);
        $image = (new Image())->setPortals($article->getIdiom()->getPortals());
        $formImage = $this->createForm(ImageType::class, $image);
        $formImage->handleRequest($request);
        
        if ($formImage->isSubmitted()) { 
            if ($formImage->isValid()) {
                $imageRepository->add($image, true);
                $article->addImage($image);
                $imageRepository->add($image, true);
                $this->idiomArticleRepository->add($article, true);
                $this->addFlash('success', "L'image a bien été ajoutée.");
                return $this->redirectToRoute('admin_app_idiom_article_gallery',  ['id' => $article->getId()]);

            } else {
                $this->addFlash('error',  "La soumission a échoué, car le formulaire n'est pas valide.");
            }
        } elseif ('POST' === $request->getMethod()) {
            $this->handleGallery($request, $article, $imageRepository);

            return $this->redirectToRoute('admin_app_idiom_article_gallery',  ['id' => $article->getId()]);
        }

        $excludes = array_map(function (Image $item) {
            return $item->getId();
        }, $article->getImages()->toArray());

        $searchData = (new SearchData())->setPage($page);
        $form = $this->createForm(AdvancedSearchType::class, $searchData, ['allow_extra_fields' => true]);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) { 
            $images = $imageRepository->search($searchData, $excludes, 15);
        } else {
            $images = $imageRepository->findPaginated($page, $excludes, 15);
        }
        
        return $this->render('Admin/idiom_article/gallery.html.twig', [
            'idiom' => $article->getIdiom(),
            'article' => $article,
            'images' => $images,
            'section_active' => true,
            'formImage' => $formImage->createView(),
            'form' => $form->createView(),
        ]);
    }

    #[Route('/articles/{id}/delete', name: 'admin_app_idiom_article_delete', methods: ['POST'])]
    public function deleteAction(Request $request, IdiomArticle $idiomArticle): Response
    {
        if ($this->isCsrfTokenValid('delete'.$idiomArticle->getId(), $request->request->get('_token'))) {
            $this->idiomArticleRepository->remove($idiomArticle, true);

            $this->addFlash('success', "L'article a été supprimé.");
        }

        return $this->redirectToRoute('admin_app_idiom_article', ['id' => $idiomArticle->getIdiom()->getId()], Response::HTTP_SEE_OTHER);
    }

    private function redirectAction(IdiomArticle $idiomArticle, Request $request): RedirectResponse
    {
        if (null !== $request->get('btn_save_and_list')) {
            return $this->redirectToRoute('admin_app_idiom_article', ['id' => $idiomArticle->getIdiom()->getId()]);
        }

        if (null !== $request->get('btn_save_and_create')) {
            return $this->redirectToRoute('admin_app_idiom_article_create', ['id' => $idiomArticle->getIdiom()->getId()]);
        }

        if (null !== $request->get('btn_save_and_edit')) {
            return $this->redirectToRoute('admin_app_idiom_article_edit', ['id' => $idiomArticle->getId()]);
        }
    }

    private function handleGallery(Request $request, IdiomArticle $article, ImageRepository $imageRepository): void
    {
        $imageId = $request->request->getInt('imageId');
        $image = $imageRepository->find($imageId);

        if (null === $image) {
            $this->addFlash('error', "L'image que vous avez choisi n'existe pas.");
        }

        $delete = $request->request->get('delete');

        if ($this->isCsrfTokenValid('image'.$image->getId(), $request->request->get('_token'))) {
            if (null === $delete) {
                $article->addImage($image);
                $this->idiomArticleRepository->add($article);
                $this->addFlash('success', "L'image a bien été ajoutée à l'article.");
            } else {
                $article->removeImage($image);
                $this->idiomArticleRepository->add($article);
                $this->addFlash('success', "L'image a bien été enlevée de l'article.");
            }
        }
    }
}