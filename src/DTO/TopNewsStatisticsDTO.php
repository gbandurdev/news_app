<?php

namespace App\DTO;

use DateTimeImmutable;

readonly class TopNewsStatisticsDTO
{
    /**
     * @param array<int, array{id: int, title: string, views: int}> $newsItems
     */
    public function __construct(
        private array              $newsItems,
        private DateTimeImmutable $periodStart,
        private DateTimeImmutable $periodEnd,
        private string             $recipientEmail
    ) {
    }

    public function getNewsItems(): array { return $this->newsItems; }
    public function getPeriodStart(): DateTimeImmutable { return $this->periodStart; }
    public function getPeriodEnd(): DateTimeImmutable { return $this->periodEnd; }
    public function getRecipientEmail(): string { return $this->recipientEmail; }
}
