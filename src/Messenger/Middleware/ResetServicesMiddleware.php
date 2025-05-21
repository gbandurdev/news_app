<?php
namespace App\Messenger\Middleware;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;

readonly class ResetServicesMiddleware implements MiddlewareInterface
{
    public function __construct(
        private ContainerInterface $container
    ) {
    }

    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        // Handle the message
        $envelope = $stack->next()->handle($envelope, $stack);

        // Reset services after handling
        if (method_exists($this->container, 'reset')) {
            $this->container->reset();
        }

        return $envelope;
    }
}
