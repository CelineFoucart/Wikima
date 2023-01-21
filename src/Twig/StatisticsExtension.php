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
        $tables = ['category', 'portal', 'article', 'image'];

        foreach ($tables as $table) {
            $this->statisticsHandler->addEntity(new SatisticsEntity($table));
        }

        $stats = $this->statisticsHandler->getStatistics();
        $pluralCat = (int) $stats['category'] > 1 ? 's' : '';
        $pluralPort = (int) $stats['portal'] > 1 ? 's' : '';
        $pluralArt = (int) $stats['article'] > 1 ? 's' : '';
        $pluralIm = (int) $stats['image'] > 1 ? 's' : '';

        return <<<HTML
            <p>
                {$this->websiteName} a <span class='fw-bold'>{$stats['category']}</span> catégorie{$pluralCat} 
                <span class="fw-bold">{$stats['portal']}</span> portail{$pluralPort} et 
                <span class="fw-bold">{$stats['article']}</span> article{$pluralArt}.
            </p>
            <p>
                Ses membres ont importé <span class='fw-bold'>{$stats['image']}</span> fichier{$pluralIm} multimédia{$pluralIm}.
            </p>
HTML;
    }
}
