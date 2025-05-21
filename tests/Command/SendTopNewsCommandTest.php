<?php

namespace App\Tests\Command;

use App\Command\SendTopNewsStatisticsCommand;
use App\DTO\TopNewsStatisticsDTO;
use App\Message\SendTopNewsStatisticsMessage;
use App\Service\MessageDispatcher;
use App\Service\NewsStatistics\NewsStatisticsService;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

class SendTopNewsCommandTest extends TestCase
{
    public function testCommandDispatchesMessage(): void
    {
        // Create a DTO to return from the service
        $dto = new TopNewsStatisticsDTO(
            [['id' => 1, 'title' => 'Test News', 'views' => 100]],
            new DateTimeImmutable('2023-01-01'),
            new DateTimeImmutable('2023-01-07'),
            'admin@example.com'
        );

        // Create mocks
        $newsStatisticsService = $this->createMock(NewsStatisticsService::class);
        $messageDispatcher = $this->createMock(MessageDispatcher::class);

        // Configure the service mock to return DTO
        $newsStatisticsService->method('generateTopNewsStatisticsDTO')
            ->willReturn($dto);

        // Set up expectations for the message dispatcher
        $messageDispatcher->expects($this->once())
            ->method('dispatch')
            ->with($this->callback(function($message) use ($dto) {
                $this->assertInstanceOf(SendTopNewsStatisticsMessage::class, $message);
                // Verify the message contains DTO
                $this->assertSame($dto, $message->getStatisticsDTO());
                return true;
            }));

        // Create command
        $command = new SendTopNewsStatisticsCommand($newsStatisticsService, $messageDispatcher);

        // Create application and add command
        $application = new Application();
        $application->add($command);
        $command = $application->find('app:send-top-news-statistics');

        // Test command execution
        $commandTester = new CommandTester($command);
        $exitCode = $commandTester->execute([]);

        // Verify command output and exit code
        $this->assertSame(Command::SUCCESS, $exitCode);
        $this->assertStringContainsString('dispatched', $commandTester->getDisplay());
    }
}
