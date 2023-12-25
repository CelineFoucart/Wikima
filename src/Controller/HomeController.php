<?php

namespace App\Controller;

use App\Entity\Data\Contact;
use App\Form\ContactType;
use App\Repository\AboutRepository;
use App\Repository\ArticleRepository;
use App\Repository\MenuItemRepository;
use App\Repository\PageRepository;
use App\Service\ContactService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(ArticleRepository $articleRepository, AboutRepository $aboutRepository, MenuItemRepository $menuItemRepository): Response
    {
        return $this->render('home/index.html.twig', [
            'article' => $articleRepository->getRandomArticle(),
            'overview' => $aboutRepository->findAboutRow('overview'),
            'menu_items' => $menuItemRepository->findBy([], ['position' => 'ASC']),
        ]);
    }

    #[Route('/contact', name: 'app_contact')]
    public function contact(Request $request, ContactService $contactService, bool $enableContact): Response
    {
        if (!$enableContact) {
            throw $this->createNotFoundException("Cette page n'existe pas.");
        }

        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $status = $contactService->notify($contact);

            if ($status) {
                $this->addFlash('success', 'Votre email a bien été envoyé.');
            } else {
                $this->addFlash('error', "L'envoi de votre email a échoué. Veuillez réessayer ultérieurement.");
            }

            return $this->redirectToRoute('app_contact');
        }

        return $this->render('home/contact.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/page-{slug}', name: 'app_page', requirements: ['slug' => '[a-z\-]*'])]
    public function page(string $slug, PageRepository $pageRepository): Response
    {
        $page = $pageRepository->findOneBy(['slug' => $slug]);

        if (null === $page) {
            throw $this->createNotFoundException();
        }

        return $this->render('home/page.html.twig', [
            'page' => $page,
            'title' => $page->getTitle(),
            'description' => $page->getDescription(),
        ]);
    }

    #[Route('/faq', name: 'app_faq')]
    public function faqAction(): Response
    {
        return $this->render('home/faq.html.twig');
    }

    #[Route('/terms', name: 'app_terms')]
    public function termsAction(): Response
    {
        return $this->render('home/terms.html.twig');
    }

    #[Route('/privacy', name: 'app_privacy')]
    public function privacyAction(): Response
    {
        return $this->render('home/privacy.html.twig');
    }
}
