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
class SettingsExtension extends AbstractExtension
{

    public function __construct(
        private UrlGeneratorInterface $urlGenerator,
        private string $faviconFile,
        private string $publicDir,
        private string $bannerFile
    ) {
    }
    
    public function getFunctions(): array
    {
        return [
            new TwigFunction('favicon', [$this, 'getFavicon'], ['is_safe' => ['html']]),
            new TwigFunction('banner', [$this, 'getBanner'], ['is_safe' => ['html']]),
        ];
    }

    public function getFavicon():string 
    {
        if (strlen($this->faviconFile) < 4 || null === $this->faviconFile) {
            return "";
        }

        $path = '/img/'. $this->faviconFile;

        if (!file_exists($this->publicDir . $path)) {
            return "";
        }

        return '<link rel="shortcut icon" href="'. $path .'">';
    }

    public function getBanner(): string 
    {
        if (strlen($this->bannerFile) < 4 || null === $this->bannerFile) {
            return "";
        }

        $path = '/img/'. $this->bannerFile;

        if (!file_exists($this->publicDir . $path)) {
            return "";
        }

        return 'style="background: linear-gradient(to bottom, rgba(0, 0, 0, 0.82), rgba(54, 54, 54, 0.2)), url('.$path.') center center / cover no-repeat"';
    }
}
