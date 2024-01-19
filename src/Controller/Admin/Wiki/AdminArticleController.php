<?php

declare(strict_types=1);

namespace App\Controller\Admin\Wiki;

use App\Entity\User;
use App\Entity\Image;
use DateTimeImmutable;
use App\Entity\Article;
use App\Entity\Section;
use App\Form\SectionType;
use App\Form\Admin\ImageType;
use App\Entity\Data\SearchData;
use App\Form\Admin\ArticleFormType;
use App\Repository\ImageRepository;
use App\Security\Voter\VoterHelper;
use App\Repository\PortalRepository;
use App\Repository\ArticleRepository;
use App\Repository\SectionRepository;
use App\Repository\TemplateRepository;
use App\Form\Search\AdvancedSearchType;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\TemplateGroupRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use App\Controller\Admin\AbstractAdminController;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/article')]
#[IsGranted(new Expression("is_granted('ROLE_ADMIN') or is_granted('ROLE_EDITOR')"))]
final class AdminArticleController extends AbstractAdminController
{
    protected string $entityName = "article";

    public function __construct(
        private ArticleRepository $articleRepository,
        private SectionRepository $sectionRepository,
        private ImageRepository $imageRepository,
        private TemplateRepository $templateRepository,
    ) {
    }

    #[Route('/', name: 'admin_app_article_list', methods:['GET'])]
    public function listAction(TemplateGroupRepository $templateGroupRepository): Response
    {
        return $this->render('Admin/article/list.html.twig', [
            'templates' => $templateGroupRepository->findBy([], ['title' => 'ASC']),
        ]);
    }

    #[Route('/archive', name: 'admin_app_article_archive_index', methods:['GET'])]
    public function archiveIndexAction(): Response
    {
        return $this->render('Admin/article/archive.html.twig', [
            'articles' => $this->articleRepository->findForAdminList(true),
        ]);
    }


    #[Route('/create', name: 'admin_app_article_create', methods:['GET', 'POST'])]
    public function createAction(Request $request, PortalRepository $portalRepository, TemplateGroupRepository $templateGroupRepository, EntityManagerInterface $em): Response
    {
        $templateId = $request->query->getInt('template', 0);
        $template =  ($templateId > 0) ? $templateGroupRepository->find($templateId) : null;
        $title = ($template) ? $template->getTitle() : '';
        $article = (new Article())->setTitle($title)->setEnableComment(true);

        $portalId = $request->query->getInt('portal');
        if (0 !== $portalId) {
            $portal = $portalRepository->find($portalId);
            if ($portal) {
                $article->addPortal($portal);
            }
        }
        
        $form = $this->createForm(ArticleFormType::class, $article);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) { 
            $article->setCreatedAt(new \DateTimeImmutable());
            $article->setAuthor($this->getUser());
            $this->articleRepository->add($article, true);
            
            if ($template !== null) {
                foreach ($template->getTemplates() as $section) {
                    $newSection = (new Section())
                        ->setTitle($section->getTitle())
                        ->setContent($section->getContent())
                        ->setCreatedAt(new DateTimeImmutable());
                    $article->addSection($newSection);
                    $em->persist($newSection);
                }

                $em->flush();
            }

            $this->addFlash('success', "L'article " . $article->getTitle() . " a bien été créé.");
            
            if (null !== $request->get('btn_save_and_section')) {
                return $this->redirectToRoute('admin_app_article_section', ['id' => $article->getId()]);
            } else {
                return $this->redirectTo($request, $article->getId());
            }
        }

        return $this->render('Admin/article/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/show', name: 'admin_app_article_show', methods:['GET', 'POST'])]
    public function showAction(Article $article, Request $request, VoterHelper $voterHelper): Response
    {
        $form = $this->createFormBuilder($article)
            ->add('author', EntityType::class, [
                'class' => User::class,
                'attr' => ['data-choices' => 'choices']
            ])
            ->getForm()
        ;

        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid() && $voterHelper->canModerate($this->getUser())) { 
            $this->articleRepository->add($article, true);
            $this->addFlash( 'success',  "L'auteur a bien été modifié.");

            return $this->redirectToRoute('admin_app_article_show', ['id' => $article->getId()]);
        }

        if ($article->isEnableComment() === null) {
            $article->setEnableComment(true);
        }

        return $this->render('Admin/article/show_general.html.twig', [
            'article' => $article,
            'general_active' => true,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_app_article_edit', methods:['GET', 'POST'])]
    public function editAction(Request $request, Article $article): Response
    {
        $this->denyAccessUnlessGranted(VoterHelper::EDIT, $article);

        if ($article->isEnableComment() === null) {
            $article->setEnableComment(true);
        }

        $form = $this->createForm(ArticleFormType::class, $article);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) { 
            $article->setUpdatedAt(new \DateTime());
            $this->articleRepository->add($article, true);
            $this->addFlash('success', "L'article " . $article->getTitle() . " a bien été modifié.");

            if (null !== $request->get('btn_save_and_section')) {
                return $this->redirectToRoute('admin_app_article_section', ['id' => $article->getId()]);
            } else {
                return $this->redirectTo($request, $article->getId());
            }
        }

        return $this->render('Admin/article/edit.html.twig', [
            'form' => $form->createView(),
            'article' => $article,
        ]);
    }

    
    #[Route('/{id}/section', name: 'admin_app_article_section', methods:['GET', 'POST'])]
    public function sectionAction(#[MapEntity(expr: 'repository.findById(id)')] Article $article, Request $request): Response
    {
        $this->denyAccessUnlessGranted(VoterHelper::EDIT, $article);
        $section = $this->getSection($request->query->getInt('section', 0), $article, $request->query->getInt('template', 0));
        $sectionForm = $this->createForm(SectionType::class, $section);
        $sectionForm->handleRequest($request);

        if ($sectionForm->isSubmitted() && $sectionForm->isValid()) {
            if (null === $section->getCreatedAt()) {
                $section->setCreatedAt(new \DateTimeImmutable());
                $lastSection = $article->getSections()->last();
                
                if ($lastSection instanceof Section) {
                    $position = $lastSection->getPosition() + 1;
                } else {
                    $position = 0;
                }
                $section->setPosition($position);

            } else {
                $section->setUpdatedAt(new \DateTime());
            }
            $this->sectionRepository->add($section);
            $this->addFlash('success', 'Les modifications ont été enregistrées.');

            return $this->redirectToRoute('admin_app_article_section', ['id' => $article->getId()]);
        }

        return $this->render('Admin/article/section.html.twig', [
            'article' => $article,
            'sectionForm' => $sectionForm,
            'section_active' => true,
            'images' => $this->imageRepository->findAll(),
            'templates' => $this->templateRepository->findBy([], ['title' => 'ASC']),
        ]);
    }

    #[Route('/{id}/gallery', name: 'admin_app_article_gallery', methods:['GET', 'POST'])]
    public function galleryAction(Article $article, Request $request): Response
    {
        $this->denyAccessUnlessGranted(VoterHelper::EDIT, $article);
        $page = $request->query->getInt('page', 1);
        $image = (new Image())->setPortals($article->getPortals());
        $formImage = $this->createForm(ImageType::class, $image);
        $formImage->handleRequest($request);

        if ($formImage->isSubmitted()) {
            if ($formImage->isValid()) {
                $this->imageRepository->add($image, true);
                $article->addImage($image);
                $this->imageRepository->add($image, true);
                $this->articleRepository->add($article, true);
                $this->addFlash('success', "L'image a bien été ajoutée.");
                return $this->redirectToRoute('admin_app_article_gallery',  ['id' => $article->getId()]);
            } else {
                $this->addFlash('error',  "La soumission a échoué, car le formulaire n'est pas valide.");
            }
        } elseif ('POST' === $request->getMethod()) {
            $this->handleGallery($request, $article);
            $uri = $request->server->get('REQUEST_URI');

            if ($uri) {
                return $this->redirect($uri);
            }

            return $this->redirectToRoute('admin_app_article_gallery', ['id' => $article->getId()]);
        }

        $excludes = array_map(function (Image $item) {
            return $item->getId();
        }, $article->getImages()->toArray());
        $searchData = (new SearchData())->setPage($page);
        $form = $this->createForm(AdvancedSearchType::class, $searchData, ['allow_extra_fields' => true]);
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
            'gallery_active' => true,
        ]);
    }

    #[Route('/{id}/archive', name: 'admin_app_article_archive', methods:['POST'])]
    public function archiveAction(Request $request, Article $article): Response
    {
        $this->denyAccessUnlessGranted(VoterHelper::EDIT, $article);

        if ($this->isCsrfTokenValid('archive'.$article->getId(), $request->request->get('_token'))) {
            $isArchived = (bool) $article->getIsArchived();
            $message = $isArchived ? "désarchivé" : "archivé";
            $article->setIsArchived(!$isArchived);
            $this->articleRepository->add($article, true);

            $this->addFlash('success', "L'article a été {$message} avec succès.");
        }

        return $this->redirectToRoute('admin_app_article_list', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/delete', name: 'admin_app_article_delete', methods:['POST'])]
    public function deleteAction(Request $request, Article $article, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted(VoterHelper::DELETE, $article);

        if ($this->isCsrfTokenValid('delete'.$article->getId(), $request->request->get('_token'))) {

            if (!$article->getIdioms()->isEmpty()) {
                foreach ($article->getIdioms() as $idiom) {
                    $article->removeIdiom($idiom);
                }

                $entityManager->persist($article);
                $entityManager->flush();
            }

            $this->articleRepository->remove($article, true);
            $this->addFlash('success', "L'élément a été supprimé avec succès.");
        }

        return $this->redirectToRoute('admin_app_article_list', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/sticky', name: 'admin_app_article_sticky', methods:['POST'])]
    public function stickyAction(Request $request, Article $article): Response
    {
        if ($this->isCsrfTokenValid('sticky'.$article->getId(), $request->request->get('_token'))) {
            $sticky = !$article->getIsSticky();
            $article->setIsSticky($sticky);
            $this->articleRepository->add($article, true);
            $this->addFlash('success', "L'article a été modifié avec succès.");
        }

        return $this->redirectToRoute('app_article_show', ['slug' => $article->getSlug()], Response::HTTP_SEE_OTHER);
    }

    private function getSection(int $sectionId, Article $article, int $templateId): Section
    {
        if ($templateId > 0) {
            $template = $this->templateRepository->find($templateId);
            $title = ($template) ? $template->getTitle() : '';
            $content = ($template) ? $template->getContent() : '';
        } else {
            $title = '';
            $content = '';
        }

        if (0 === $sectionId) {
            return (new Section())->setArticle($article)->setTitle($title)->setContent($content);
        }
        

        $section = $this->sectionRepository->findOneByArticle($sectionId, $article->getId());

        if (!$section) {
            $section = (new Section())->setArticle($article);
        }

        return $section;
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
}