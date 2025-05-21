<?php

namespace App\Controller\Traits;

use Exception;
use Symfony\Component\HttpFoundation\Response;

trait FlashMessageTrait
{
    /**
     * Handle successful operation with flash message and redirect
     */
    protected function handleSuccess(string $message, string $route, array $routeParams = []): Response
    {
        $this->addFlash('success', $message);
        return $this->redirectToRoute($route, $routeParams);
    }

    /**
     * Handle failed operation with flash message and render template
     */
    protected function handleError(Exception $e, string $template, array $templateParams = []): Response
    {
        $this->addFlash('error', $e->getMessage());
        return $this->render($template, $templateParams);
    }

    /**
     * Execute operation with try-catch and flash messages
     */
    protected function executeWithFlash(
        callable $operation,
        string $successMessage,
        string $successRoute,
        string $errorTemplate,
        array $routeParams = [],
        array $templateParams = []
    ): Response {
        try {
            $operation();
            return $this->handleSuccess($successMessage, $successRoute, $routeParams);
        } catch (Exception $e) {
            return $this->handleError($e, $errorTemplate, $templateParams);
        }
    }
}
