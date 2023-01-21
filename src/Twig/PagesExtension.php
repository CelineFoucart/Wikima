<?php

namespace App\Twig;

use App\Repository\PageRepository;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Class PagesExtension
 * 
 * PagesExtension displays user's pages for a twig view.
 * 
 * @author Céline Foucart <celinefoucart@yahoo.fr>
 */
class PagesExtension extends AbstractExtension
{

    public function __construct(
        private PageRepository $pageRepository,
        private UrlGeneratorInterface $urlGenerator
    ) { 
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('get_pages', [$this, 'getPages'], ['is_safe' => ['html']]),
        ];
    }
    
    public function getPages(): string
    {
        $pages = $this->pageRepository->findAll();

        if (count($pages) === 0) {
            return '';
        }

        $html = '<li><hr class="dropdown-divider"></li>';

        foreach ($pages as $page) {
            $url = $this->urlGenerator->generate('app_page', ["slug" => $page->getSlug()]);
            $html .= '<li><a class="dropdown-item" href="'.$url.'">'.$page->getTitle().'</a></li>';
        }

        return $html;
    }
}
