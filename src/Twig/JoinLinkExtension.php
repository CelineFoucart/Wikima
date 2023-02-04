<?php

namespace App\Twig;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class JoinLinkExtension  extends AbstractExtension
{
    public function __construct(
        private UrlGeneratorInterface $urlGenerator
    ) { }
    
    public function getFunctions(): array
    {
        return [
            new TwigFunction('join_links', [$this, 'joinLinks'], ['is_safe' => ['html']]),
        ];
    }

    public function joinLinks(array $elements, string $routeName): string
    {
        $parts = [];

        foreach($elements as $element) {
            $route = $this->urlGenerator->generate($routeName, ['slug' => $element->getSlug()]);

            $parts[] = '<a href="'.$route.'" class="text-decoration-none">'. $element .'</a>';
        }

        $html = join(', ', $parts);

        return $html;
    }
}
