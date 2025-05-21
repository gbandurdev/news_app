<?php
namespace App\Service\Email;

use App\Config\EmailConfig;
use App\DTO\TopNewsStatisticsDTO;
use Exception;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Twig\Environment;

readonly class NewsEmailService
{
    public function __construct(
        private MailerInterface $mailer,
        private EmailConfig     $emailConfig,
        private Environment     $twig
    ) {
    }

    public function sendNewsStatisticsEmail(TopNewsStatisticsDTO $dto): bool
    {
        try {
            $subject = sprintf(
                'Top 10 News Statistics (%s - %s)',
                $dto->getPeriodStart()->format('Y-m-d'),
                $dto->getPeriodEnd()->format('Y-m-d')
            );

            $htmlContent = $this->twig->render('emails/news_statistics.html.twig', [
                'news_items' => $dto->getNewsItems(),
                'period_start' => $dto->getPeriodStart(),
                'period_end' => $dto->getPeriodEnd()
            ]);

            $textContent = $this->twig->render('emails/news_statistics.txt.twig', [
                'news_items' => $dto->getNewsItems(),
                'period_start' => $dto->getPeriodStart(),
                'period_end' => $dto->getPeriodEnd()
            ]);

            $email = (new Email())
                ->from(new Address(
                    $this->emailConfig->getSenderEmail(),
                    $this->emailConfig->getSenderName()
                ))
                ->to($dto->getRecipientEmail())
                ->subject($subject)
                ->html($htmlContent)
                ->text($textContent);

            $this->mailer->send($email);
            return true;
        } catch (Exception $e) {
            // Log the error
            return false;
        }
    }
}
