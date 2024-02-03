<?php

namespace App\Twig;

use App\Repository\ReportRepository;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

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
