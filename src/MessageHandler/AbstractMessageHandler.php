<?php
namespace App\MessageHandler;

use Exception;
use Psr\Log\LoggerInterface;

abstract class AbstractMessageHandler
{
    public function __construct(
        protected readonly LoggerInterface $logger
    ) {
    }

    /**
     * @throws Exception
     */
    protected function handle(object $message): void
    {
        try {
            $this->logStart($message);
            $this->process($message);
            $this->logSuccess($message);
        } catch (Exception $e) {
            $this->logError($message, $e);
            throw $e;
        }
    }

    abstract protected function process(object $message): void;

    protected function logStart(object $message): void
    {
        $this->logger->info('Starting to process message', [
            'message_class' => get_class($message)
        ]);
    }

    protected function logSuccess(object $message): void
    {
        $this->logger->info('Successfully processed message', [
            'message_class' => get_class($message)
        ]);
    }

    protected function logError(object $message, Exception $e): void
    {
        $this->logger->error('Error processing message', [
            'message_class' => get_class($message),
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
    }
}
