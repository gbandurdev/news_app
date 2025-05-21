<?php
namespace App\Message;

use App\DTO\TopNewsStatisticsDTO;

readonly class SendTopNewsStatisticsMessage implements AsyncMessageInterface
{
    public function __construct(
        private TopNewsStatisticsDTO $statisticsDTO
    ) {
    }

    public function getStatisticsDTO(): TopNewsStatisticsDTO
    {
        return $this->statisticsDTO;
    }
}
