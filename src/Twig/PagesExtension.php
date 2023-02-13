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

        $html = '<li><hr class="dropdown-divider"></li><li><h6 class="dropdown-header">Pages</li>';

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
            $html .= '<li><a class="dropdown-item" href="'.$path.'">' . $category . '</a>';

            if (!$category->getPortals()->isEmpty()) {
                $html .= '<ul class="dropdown-menu dropdown-submenu">';

                foreach ($category->getPortals() as $portal) {
                    $portalPath =  $this->urlGenerator->generate('app_portal_show', ["slug" => $portal->getSlug()]);
                    $html .= '<li><a class="dropdown-item" href="'.$portalPath.'">'.$portal->getTitle().'</a></li>';
                }
                $html .= '</ul>';
            } else {
                $html .= '</a>';
            }
            
            $html .= '</li>';
        }

        return $html;
    }
}
