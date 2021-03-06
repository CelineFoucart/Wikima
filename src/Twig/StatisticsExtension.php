<?php

namespace App\Twig;

use App\Service\Statistics\SatisticsEntity;
use App\Service\Statistics\StatisticsHandler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Class StatisticsExtension
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
    ) { }
    
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
        $tables = ['category', 'portal', 'article'];

        foreach ($tables as $table) {
            $this->statisticsHandler->addEntity(new SatisticsEntity($table));
        }

        $stats = $this->statisticsHandler->getStatistics();

        return <<<HTML
            <p>
                {$this->websiteName} a <span class='fw-bold'>{$stats['category']}</span> catégories 
                <span class="fw-bold">{$stats['portal']}</span> portails et 
                <span class="fw-bold">{$stats['article']}</span> articles.
            </p>
            <p>
                Ses membres ont importé <span class='fw-bold'>13</span> fichiers multimédias.
            </p>
HTML;
    }
}
