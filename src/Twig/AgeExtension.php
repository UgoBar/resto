<?php

namespace App\Twig;

use App\Service\GetAgeService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AgeExtension extends AbstractExtension
{
    public function __construct(
        private GetAgeService $ageService
    ){ }

    public function getFilters()
    {
        return [
            new TwigFilter('getAge', [$this, 'getAge']),
        ];
    }

    public function getAge($date): string
    {
        return $this->ageService->getAgeFromDate($date);
    }
}
