<?php

namespace App\Twig;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Class ActiveLinkExtension
 * 
 * ActiveLinkExtension handles active links.
 * 
 * @author Céline Foucart <celinefoucart@yahoo.fr>
 */
class ActiveLinkExtension extends AbstractExtension
{

    public function __construct(
        private UrlGeneratorInterface $urlGenerator
    ) { }
    
    public function getFunctions(): array
    {
        return [
            new TwigFunction('active_link', [$this, 'isActive']),
            new TwigFunction('active_links', [$this, 'areActive']),
        ];
    }

    /**
     * Determines if a link is active.
     */
    public function isActive(Request $request, string $routeName, bool $strict = true): string
    {
        $isActive = $this->isLinkActive($request, $routeName, $strict);

        return ($isActive) ? 'active' : '';
    }

    /**
     * Determines if a there is an active link in a group of links.
     */
    public function areActive(Request $request, array $routes, bool $isStricted = false): string
    {
        foreach ($routes as $routeName) {

            $isActive = $this->isLinkActive($request, $routeName, $isStricted);

            if ($isActive) {
                return 'active';
            }
        }

        return '';
    }

    /**
     * Determines if a link is active.
     */
    private function isLinkActive(Request $request, string $routeName, bool $isStricted = false): bool
    {
        $currentRoute = $request->get('_route');

        if ($isStricted) {
            return $currentRoute === $routeName;
        }

        $parts = explode('_', $currentRoute);

        return in_array($routeName, $parts);

    }
}
