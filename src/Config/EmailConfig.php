<?php

namespace App\Config;

use Symfony\Component\DependencyInjection\Attribute\Autowire;

readonly class EmailConfig
{
    public function __construct(
        #[Autowire('%env(MAILER_SENDER_EMAIL)%')]
        private string $senderEmail,

        #[Autowire('%env(MAILER_SENDER_NAME)%')]
        private string $senderName,

        #[Autowire('%env(MAILER_ADMIN_EMAIL)%')]
        private string $adminEmail
    ) {
    }

    public function getSenderEmail(): string
    {
        return $this->senderEmail;
    }

    public function getSenderName(): string
    {
        return $this->senderName;
    }

    public function getAdminEmail(): string
    {
        return $this->adminEmail;
    }
}
