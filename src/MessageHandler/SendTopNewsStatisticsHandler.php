<?php

namespace App\MessageHandler;

use App\Message\SendTopNewsStatisticsMessage;
use App\Service\NewsStatistics\NewsStatisticsService;
use DateTimeImmutable;
use Exception;
use InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class SendTopNewsStatisticsHandler extends AbstractMessageHandler
{
    public function __construct(
        private readonly NewsStatisticsService $newsStatisticsService,
        LoggerInterface $logger
    ) {
        parent::__construct($logger);
    }

    /**
     * This is the method Symfony Messenger will call
     * @throws Exception
     */
    public function __invoke(SendTopNewsStatisticsMessage $message): void
    {
        $this->handle($message);
    }

    protected function process(object $message): void
    {
        if (!$message instanceof SendTopNewsStatisticsMessage) {
            throw new InvalidArgumentException('Invalid message type');
        }

        $dto = $message->getStatisticsDTO();

        // Log email details before sending
        $this->logger->info('Preparing to send statistics email', [
            'recipient' => $dto->getRecipientEmail(),
            'period_start' => $dto->getPeriodStart()->format('Y-m-d'),
            'period_end' => $dto->getPeriodEnd()->format('Y-m-d'),
            'news_count' => count($dto->getNewsItems())
        ]);

        // Send the email
        $this->newsStatisticsService->sendTopNewsStatistics($dto);

        // Log success with detailed information
        $this->logger->info('Successfully sent statistics email', [
            'recipient' => $dto->getRecipientEmail(),
            'sent_at' => (new DateTimeImmutable())->format('Y-m-d H:i:s'),
            'subject' => sprintf(
                'Top 10 News Statistics (%s - %s)',
                $dto->getPeriodStart()->format('Y-m-d'),
                $dto->getPeriodEnd()->format('Y-m-d')
            )
        ]);
    }
}
