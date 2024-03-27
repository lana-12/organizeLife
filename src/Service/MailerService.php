<?php

namespace App\Service;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;

class MailerService
{

    public function __construct(private MailerInterface $mailerInt)
    {}

    public function sendEmail(string $to, string $from, string $subject, string $template, array $context): void
    {
        $email = (new TemplatedEmail())
            ->to($to)
            ->from($from)
            ->subject($subject)
            ->htmlTemplate($template)
            ->context($context);

        $this->mailerInt->send($email);
    }

}
