<?php
namespace App\Service\NewsStatistics;

use App\DTO\TopNewsStatisticsDTO;

interface NewsStatisticsServiceInterface
{
    public function generateTopNewsStatisticsDTO(): TopNewsStatisticsDTO;
    public function sendTopNewsStatistics(?TopNewsStatisticsDTO $dto = null): bool;
    public function reset(): void;
}
