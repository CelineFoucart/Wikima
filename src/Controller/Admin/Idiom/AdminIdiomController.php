<?php

namespace App\Controller\Admin\Idiom;

use DateTime;
use App\Entity\User;
use App\Entity\Idiom;
use DateTimeImmutable;
use App\Entity\IdiomArticle;
use App\Form\Admin\IdiomFormType;
use App\Repository\IdiomRepository;
use App\Repository\ImageRepository;
use App\Security\Voter\VoterHelper;
use App\Repository\PortalRepository;
use App\Repository\ArticleRepository;
use App\Service\IdiomNavigationHelper;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\TemplateGroupRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use App\Controller\Admin\AbstractAdminController;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/idiom')]
#[IsGranted(new Expression("is_granted('ROLE_ADMIN') or is_granted('ROLE_EDITOR')"))]
class AdminIdiomController extends AbstractAdminController
{
    protected string $entityName = 'idiom';

    public function __construct(
        private ImageRepository $imageRepository,
        private SluggerInterface $slugger,
        bool $enableIdiom
    ) {
        if (false === $enableIdiom) {
            throw $this->createNotFoundException('Not Found');
        }
    }

    #[Route('/', name: 'admin_app_idiom_list', methods: ['GET'])]
    public function listAction(IdiomRepository $idiomRepository, TemplateGroupRepository $templateGroupRepository): Response
    {
        return $this->render('Admin/idiom/list.html.twig', [
            'idioms' => $idiomRepository->findAll(),
            'templates' => $templateGroupRepository->findBy([], ['title' => 'ASC']),
        ]);
    }

    #[Route('/create', name: 'admin_app_idiom_create', methods: ['GET', 'POST'])]
    public function createAction(Request $request, EntityManagerInterface $entityManager, PortalRepository $portalRepository, TemplateGroupRepository $templateGroupRepository): Response
    {
        $templateId = $request->query->getInt('template', 0);
        $template =  ($templateId > 0) ? $templateGroupRepository->find($templateId) : null;
        $title = ($template) ? $template->getTitle() : '';
        $idiom = (new Idiom())->setTranslatedName($title);

        $portalId = $request->query->getInt('portal');
        if (0 !== $portalId) {
            $portal = $portalRepository->find($portalId);
            if ($portal) {
                $idiom->addPortal($portal);
            }
        }

        $form = $this->createForm(IdiomFormType::class, $idiom);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $idiom->setCreatedAt(new DateTimeImmutable())->setAuthor($this->getUser());
            $entityManager->persist($idiom);
            $entityManager->flush();

            if ($template !== null) {
                foreach ($template->getTemplates() as $section) {
                    $newArticle = (new IdiomArticle())
                        ->setTitle($section->getTitle())
                        ->setContent($section->getContent())
                        ->setCreatedAt(new DateTimeImmutable())
                        ->setSlug($this->slugger->slug(strtolower($section->getTitle() . '-' . $idiom->getId())))
                        ->setIdiom($idiom);
                    $idiom->addIdiomArticle($newArticle);
                    $entityManager->persist($newArticle);
                }

                $entityManager->flush();
            }

            $this->addFlash('success', 'La langue '.$idiom.' a bien été créée.');

            return $this->redirectTo($request, $idiom->getId());
        }

        return $this->render('Admin/idiom/create.html.twig', [
            'idiom' => $idiom,
            'form' => $form,
            'images' => $this->imageRepository->findAll(),
        ]);
    }

    #[Route('/{id}/show', name: 'admin_app_idiom_show', methods: ['GET', 'POST'])]
    public function showAction(Idiom $idiom, EntityManagerInterface $entityManager, ArticleRepository $articleRepository, Request $request, VoterHelper $voterHelper): Response
    {
        $form = $this->createFormBuilder($idiom)
            ->add('author', EntityType::class, [
                'class' => User::class,
                'attr' => ['data-choices' => 'choices']
            ])
            ->getForm()
        ;

        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid() && $voterHelper->canModerate($this->getUser())) { 
            $entityManager->persist($idiom);
            $entityManager->flush();
            $this->addFlash( 'success',  "L'auteur a bien été modifié.");

            return $this->redirectToRoute('admin_app_idiom_show', ['id' => $idiom->getId()]);
        }
        
        if ($request->isMethod('POST')) {
            $this->denyAccessUnlessGranted(VoterHelper::EDIT, $idiom,'Access Denied.');
            $id = $request->request->get('article');
            $article = ($id !== null) ? $articleRepository->find($id) : null;

            if ($article) {
                $idiom->setArticle($article);
                $entityManager->persist($idiom);
                $entityManager->flush();
                $this->addFlash('success', "L'article a bien été lié à la langue.");
            }
        }

        return $this->render('Admin/idiom/show.html.twig', [
            'idiom' => $idiom,
            'form' => $form->createView(),
            'general_active' => true,
        ]);
    }

    #[Route('/{id}/order', name: 'admin_app_idiom_order', methods: ['GET', 'POST'])]
    public function orderAction(#[MapEntity(expr: 'repository.findIdiomById(id)')] Idiom $idiom): Response
    {
        $this->denyAccessUnlessGranted(VoterHelper::EDIT, $idiom,'Access Denied.');
        
        return $this->render('Admin/idiom/order.html.twig', [
            'idiom' => $idiom,
            'order_active' => true,
            'navigations' => IdiomNavigationHelper::generateNavigation($idiom),
        ]);
    }

    #[Route('/{id}/articles', name: 'admin_app_idiom_article', methods: ['GET', 'POST'])]
    public function articlesAction(Idiom $idiom): Response
    {
        $this->denyAccessUnlessGranted(VoterHelper::EDIT, $idiom,'Access Denied.');
        
        return $this->render('Admin/idiom/articles.html.twig', [
            'idiom' => $idiom,
            'section_active' => true,
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_app_idiom_edit', methods: ['GET', 'POST'])]
    public function editAction(Request $request, Idiom $idiom, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted(VoterHelper::EDIT, $idiom);
        $form = $this->createForm(IdiomFormType::class, $idiom);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $idiom->setUpdatedAt(new DateTime());
            $entityManager->persist($idiom);
            $entityManager->flush();
            $this->addFlash('success', 'La langue '.$idiom.' a bien été modifiée.');

            return $this->redirectTo($request, $idiom->getId());
        }

        return $this->render('Admin/idiom/edit.html.twig', [
            'idiom' => $idiom,
            'form' => $form,
            'images' => $this->imageRepository->findAll(),
        ]);
    }

    #[Route('/{id}/delete', name: 'admin_app_idiom_delete', methods: ['POST'])]
    public function deleteAction(Request $request, Idiom $idiom, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted(VoterHelper::DELETE, $idiom);

        if ($this->isCsrfTokenValid('delete'.$idiom->getId(), $request->request->get('_token'))) {
            $entityManager->remove($idiom);
            $entityManager->flush();
            $this->addFlash('success', 'La langue a été supprimée avec succès.');
        }

        return $this->redirectToRoute('admin_app_idiom_list', [], Response::HTTP_SEE_OTHER);
    }
}
