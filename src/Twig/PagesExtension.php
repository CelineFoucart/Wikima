<?php

namespace App\Twig;

use App\Repository\CategoryRepository;
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
        private UrlGeneratorInterface $urlGenerator,
        private CategoryRepository $categoryRepository,
    ) { 
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('get_pages', [$this, 'getPages'], ['is_safe' => ['html']]),
            new TwigFunction('get_categories', [$this, 'getCategories'], ['is_safe' => ['html']]),
        ];
    }
    
    public function getPages(): string
    {
        $pages = $this->pageRepository->findAll();

        if (count($pages) === 0) {
            return '';
        }

        $html = '<li><hr class="dropdown-divider"></li><li><h6 class="dropdown-header">Pages</h6></li>';

        foreach ($pages as $page) {
            $url = $this->urlGenerator->generate('app_page', ["slug" => $page->getSlug()]);
            $html .= '<li><a class="dropdown-item" href="'.$url.'">'.$page->getTitle().'</a></li>';
        }

        return $html;
    }

    public function getCategories(): string
    {
        $categories = $this->categoryRepository->findWithPortals();
        $html = '';

        if (count($categories) === 0) {
            return $html;
        }

        foreach ($categories as $category) {
            $path =  $this->urlGenerator->generate('app_category_show', ["slug" => $category->getSlug()]);
            $html .= '<div class="search-item"><a class="dropdown-item fw-bold" href="'.$path.'"><i class="fas fa-folder me-1"></i>' . $category . '</a></div>';

            foreach ($category->getPortals() as $portal) {
                $portalPath =  $this->urlGenerator->generate('app_portal_show', ["slug" => $portal->getSlug()]);
                $html .= '<div class="search-item"><a class="dropdown-item" href="'.$portalPath.'"><i class="fas fa-tag me-1"></i>'.$portal->getTitle().'</a></div>';
            }
        }

        return $html;
    }
}
