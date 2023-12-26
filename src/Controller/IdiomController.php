<?php

namespace App\Controller;

use App\Entity\Idiom;
use App\Entity\IdiomArticle;
use App\Repository\IdiomRepository;
use App\Service\IdiomNavigationHelper;
use App\Service\LogService;
use App\Service\Word\WordIdiomArticleGenerator;
use App\Service\Word\WordIdiomGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class IdiomController extends AbstractController
{
    public function __construct(bool $enableIdiom)
    {
        if (false === $enableIdiom) {
            throw $this->createNotFoundException('Not Found');
        }
    }
    
    #[Route('/idioms', name: 'app_idiom_index')]
    public function indexAction(IdiomRepository $idiomRepository): Response
    {
        return $this->render('idiom/index_idiom.html.twig', [
            'idioms' => $idiomRepository->findAll(),
        ]);
    }

    #[Route('/idioms/{slug}', name: 'app_idiom_show')]
    public function showIdiomAction(#[MapEntity(expr: 'repository.findIdiomBySlug(slug)')] Idiom $idiom): Response
    {
        return $this->render('idiom/show_idiom.html.twig', [
            'idiom' => $idiom,
            'navigations' => IdiomNavigationHelper::generateNavigation($idiom),
        ]);
    }

    #[Route('/idioms/{slug}/word', name: 'app_idiom_word')]
    public function wordAction(#[MapEntity(expr: 'repository.findIdiomBySlug(slug)')] Idiom $idiom, WordIdiomGenerator $generator, LogService $logService): Response
    {
        try {
            $file = $generator->setIdiom($idiom)->generate();
            $response = new BinaryFileResponse($file['path']);
            $response->setContentDisposition(
                ResponseHeaderBag::DISPOSITION_ATTACHMENT,
                $file['filename']
            );
            $response->deleteFileAfterSend();

            return $response;
        } catch (\Exception $th) {
            $this->addFlash('error', "Le fichier n'a pas pu être généré, car il y a des liens vers des images invalides.");
            $logService->error("Génération de '{$idiom->getSlug()}.docx'", $th->getMessage(), 'Idiom');

            return $this->redirectToRoute('app_idiom_show', ['slug' => $idiom->getSlug()]);
        }
    }

    #[Route('/idioms/{idiom}/{article}', name: 'app_idiom_show_article')]
    public function showArticleAction(
        #[MapEntity(expr: 'repository.findIdiomBySlug(idiom)')] Idiom $idiomEntity, 
        #[MapEntity(expr: 'repository.findOneBySlug(article)')]  IdiomArticle $idiomArticle
    ): Response {
        return $this->render('idiom/show_idiom_article.html.twig', [
            'idiom' => $idiomEntity,
            'article' => $idiomArticle,
            'navigations' => IdiomNavigationHelper::generateNavigation($idiomEntity),
        ]);
    }

    #[Route('/idioms/articles/{article}/word', name: 'app_idiom_show_article_word')]
    public function idiomArticleWordAction(
        #[MapEntity(expr: 'repository.findOneBySlug(article)')] IdiomArticle $idiomArticle, 
        WordIdiomArticleGenerator $generator,
        LogService $logService
    ): Response {
        try {
            $file = $generator->setArticle($idiomArticle)->generate();
            $response = new BinaryFileResponse($file['path']);
            $response->setContentDisposition(
                ResponseHeaderBag::DISPOSITION_ATTACHMENT,
                $file['filename']
            );
            $response->deleteFileAfterSend();

            return $response;
        } catch (\Exception $th) {
            $filename = "{$idiomArticle->getIdiom()->getSlug()}-{$idiomArticle->getSlug()}.docx";
            $this->addFlash('error', "Le fichier n'a pas pu être généré, car il y a des liens vers des images invalides.");
            $logService->error("Génération de '{$filename}'", $th->getMessage(), 'IdiomArticle');

            return $this->redirectToRoute('app_idiom_show_article', [
                'idiom' => $idiomArticle->getIdiom()->getSlug(),
                'article' => $idiomArticle->getSlug(),
            ]);
        }
    }
}