<?php

namespace App\Tests\DTO;

use App\DTO\TopNewsStatisticsDTO;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class TopNewsStatisticsDTOTest extends TestCase
{
    public function testDtoReturnsCorrectValues(): void
    {
        $newsItems = [
            ['id' => 1, 'title' => 'Test News 1', 'views' => 100],
            ['id' => 2, 'title' => 'Test News 2', 'views' => 200],
        ];

        $periodStart = new DateTimeImmutable('2025-01-01');
        $periodEnd = new DateTimeImmutable('2025-01-07');
        $recipientEmail = 'test@example.com';

        $dto = new TopNewsStatisticsDTO(
            $newsItems,
            $periodStart,
            $periodEnd,
            $recipientEmail
        );

        $this->assertSame($newsItems, $dto->getNewsItems());
        $this->assertSame($periodStart, $dto->getPeriodStart());
        $this->assertSame($periodEnd, $dto->getPeriodEnd());
        $this->assertSame($recipientEmail, $dto->getRecipientEmail());
    }
}
