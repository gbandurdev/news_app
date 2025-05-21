<?php
namespace App\Scheduler;

use App\Command\SendTopNewsStatisticsCommand;
use Symfony\Component\Scheduler\Attribute\AsSchedule;
use Symfony\Component\Scheduler\RecurringMessage;
use Symfony\Component\Scheduler\Schedule;
use Symfony\Component\Scheduler\ScheduleProviderInterface;

#[AsSchedule('weekly_news')]
readonly class WeeklyNewsStatisticsScheduler implements ScheduleProviderInterface
{
    public function __construct(
        private SendTopNewsStatisticsCommand $command
    ) {
    }

    public function getSchedule(): Schedule
    {
        $schedule = new Schedule();
        // Every Monday at 9:00 AM
        $timezone = 'UTC';
        $recurringMessage = RecurringMessage::cron('0 9 * * 1', $this->command, $timezone);

        $schedule->add($recurringMessage);

        return $schedule;
    }
}
