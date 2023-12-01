<?php

namespace App\Twig;

use Twig\TwigFunction;
use App\Repository\ReportRepository;
use Twig\Extension\AbstractExtension;

class ForumExtension extends AbstractExtension
{
    public function __construct(private ReportRepository $reportRepository)
    {
        
    }
    
    public function getFunctions(): array
    {
        return [
            new TwigFunction('report_total', [$this, 'getReportsTotal']),
        ];
    }

    public function getReportsTotal(): int
    {
        return $this->reportRepository->count([]);
    }
}
