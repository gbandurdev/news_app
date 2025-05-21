<?php
namespace App\Command;

use App\Message\SendTopNewsStatisticsMessage;
use App\Service\MessageDispatcher;
use App\Service\NewsStatistics\NewsStatisticsService;
use Exception;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Messenger\Exception\ExceptionInterface;

#[AsCommand(
    name: 'app:send-top-news-statistics',
    description: 'Dispatches weekly Top 10 news statistics task to queue',
)]
class SendTopNewsStatisticsCommand extends Command
{
    public function __construct(
        private readonly NewsStatisticsService $newsStatisticsService,
        private readonly MessageDispatcher $messageDispatcher
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setHelp('This command dispatches a task to send the Top 10 most viewed news to the configured recipient');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        try {
            $io->info('Dispatching top news statistics task to queue...');

            // Generate the DTO with the necessary data
            $statisticsDTO = $this->newsStatisticsService->generateTopNewsStatisticsDTO();

            // Create and dispatch the message
            $message = new SendTopNewsStatisticsMessage($statisticsDTO);
            $this->messageDispatcher->dispatch($message);

            $io->success('Top news statistics task has been dispatched to queue.');

            return Command::SUCCESS;
        } catch (Exception $e) {
            $io->error(sprintf('An error occurred while dispatching task: %s', $e->getMessage()));
            return Command::FAILURE;
        } catch (ExceptionInterface|InvalidArgumentException $e) {
            return Command::FAILURE;
        }
    }
}
