<?php

namespace App\Service;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Psr\Log\LoggerInterface;

/**
 * This service sends templated emails using Symfony Mailer and Twig.
 */
class MailerService
{
    private string $defaultSender;
    private LoggerInterface $logger;

    /**
     * @param MailerInterface $mailerInt The mailer service
     * @param LoggerInterface $logger Logger for error reporting
     * @param string $defaultSender Default sender email address 
     */
    public function __construct(
        private MailerInterface $mailerInt,
        LoggerInterface $logger,
        string $defaultSender = 'organizelife34@gmail.com'
    ) {
        $this->logger = $logger;
        $this->defaultSender = $defaultSender;
    }

    /**
     * Sends an email using a Twig HTML template.
     *
     * @param string $to Recipient email address
     * @param string|null $from Sender email (optional)
     * @param string $subject Email subject
     * @param string $template Twig template path (e.g. 'emails/welcome.html.twig')
     * @param array $context Variables passed to the Twig template
     *
     * @return void
     */
    public function sendEmail(string $to, ?string $from, string $subject, string $template, array $context): void
    {
        $email = (new TemplatedEmail())
            ->to($to)
            ->from($from ?? $this->defaultSender)
            ->subject($subject)
            ->htmlTemplate($template)
            ->context($context);

        try {
            $this->mailerInt->send($email);
            $this->logger->info("Email sent to {$to} with subject '{$subject}'");
        } catch (TransportExceptionInterface $e) {
            $this->logger->error("Failed to send email to {$to}: " . $e->getMessage());
        }
    }
}
