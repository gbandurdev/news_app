<?php

namespace App\Service;

use App\Message\AsyncMessageInterface;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * Service responsible for dispatching asynchronous messages to the queue.
 *
 * This class provides an abstraction layer over Symfony's MessageBus, offering:
 * - Type safety through the AsyncMessageInterface requirement
 * - A centralized point for controlling message dispatching
 * - Simplified testing by providing a single service to mock
 * - Decoupling from Symfony's messenger implementation details
 */
class MessageDispatcher
{
    /**
     * @param MessageBusInterface $messageBus The Symfony message bus service
     */
    public function __construct(
        private readonly MessageBusInterface $messageBus
    ) {
    }

    /**
     * Dispatches a message to be processed asynchronously.
     *
     * @param AsyncMessageInterface $message The message to dispatch to the queue
     * @throws ExceptionInterface If the message cannot be dispatched
     *
     * TODO: logging or monitoring for message dispatching
     * TODO: If needed, add support for custom message attributes or metadata
     */
    public function dispatch(AsyncMessageInterface $message): void
    {
        $this->messageBus->dispatch($message);
    }
}
