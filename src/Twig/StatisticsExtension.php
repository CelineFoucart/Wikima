<?php

namespace App\Twig;

use App\Service\Statistics\SatisticsEntity;
use App\Service\Statistics\StatisticsHandler;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Class StatisticsExtension.
 *
 * StatisticsExtension displays statistics information.
 *
 * @author Céline Foucart <celinefoucart@yahoo.fr>
 */
class StatisticsExtension extends AbstractExtension
{
    public function __construct(
        private StatisticsHandler $statisticsHandler,
        private string $websiteName
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('get_statistics', [$this, 'getStatistics'], ['is_safe' => ['html']]),
        ];
    }

    /**
     * Determines if a link is active.
     */
    public function getStatistics(): string
    {
        $tables = ['category', 'portal', 'article', 'image', 'place', 'person'];

        foreach ($tables as $table) {
            $this->statisticsHandler->addEntity(new SatisticsEntity($table));
        }

        $stats = $this->statisticsHandler->getStatistics();
        $pluralCat = (int) $stats['category'] > 1 ? 's' : '';
        $pluralPort = (int) $stats['portal'] > 1 ? 's' : '';
        $pluralArt = (int) $stats['article'] > 1 ? 's' : '';
        $pluralIm = (int) $stats['image'] > 1 ? 's' : '';
        $pluralPl = (int) $stats['place'] > 1 ? 'x' : '';
        $pluralPe = (int) $stats['person'] > 1 ? 's' : '';

        $total = (int) $stats['article'] + (int) $stats['place'] + (int) $stats['person'] + (int) $stats['image'];
        $plural = $total > 1 ? 's' : '';

        return <<<HTML
            <p> 
                Aujourd'hui, {$this->websiteName} regroupe 
                dans <strong>{$stats['category']}</strong> catégorie{$pluralCat} et <strong>{$stats['portal']}</strong> portail{$pluralPort} 
                les <strong>{$total}</strong> élément{$plural} suivants :
            </p>
            <p class="d-flex flex-wrap justify-content-between mx-5 text-center">
                <span class="px-1">
                    <span class="fw-bold fs-5">{$stats['article']}</span> <br> 
                    article{$pluralArt}
                </span>
                <span class="px-1">
                    <span class='fw-bold fs-5'>{$stats['image']}</span> <br>
                    image{$pluralIm} 
                </span>
                <span class="px-1">
                    <span class="fw-bold fs-5">{$stats['place']}</span> <br>
                    lieu{$pluralPl} 
                </span>
                <span class="px-1">
                    <span class="fw-bold fs-5">{$stats['person']}</span> <br>
                    personnage{$pluralPe}
                </span>
            </p>
HTML;
    }
}
