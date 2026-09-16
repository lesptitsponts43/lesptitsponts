<?php

namespace App\Service;

use App\Entity\Commande;
use App\Entity\Contact;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportException;
use Symfony\Component\Mailer\MailerInterface;
use Twig\Environment;

class EmailService
{
    public function __construct(
        private MailerInterface $mailer,
        private Environment $twig,
        private string $fromEmail,
        private string $adminEmail
    ) {
    }

    public function sendPaymentConfirmation(Commande $commande): void
    {
        if (!$commande->getEmail()) {
            return;
        }

        $email = (new TemplatedEmail())
            ->from($this->fromEmail)
            ->to($commande->getEmail())
            ->replyTo($this->adminEmail)
            ->subject('Confirmation de votre commande ' . $commande->getNumeroCommande())
            ->htmlTemplate('emails/commande_payee_client.html.twig')
            ->context([
                'commande' => $commande,
            ]);

        try {
            $this->mailer->send($email);
        } catch (TransportException $e) {
            // Keep the payment flow working even if the mail transport is unavailable.
        }
    }

    public function sendAdminNewOrder(Commande $commande): void
    {
        if (!$this->adminEmail) {
            return;
        }

        $email = (new TemplatedEmail())
            ->from($this->fromEmail)
            ->to($this->adminEmail)
            ->subject('Nouvelle commande payée : ' . $commande->getNumeroCommande())
            ->htmlTemplate('emails/commande_payee_admin.html.twig')
            ->context([
                'commande' => $commande,
            ]);

        try {
            $this->mailer->send($email);
        } catch (TransportException $e) {
            // Keep the payment flow working even if the mail transport is unavailable.
        }
    }

    public function sendAdminContactNotification(Contact $contact): void
    {
        if (!$this->adminEmail) {
            return;
        }

        $email = (new TemplatedEmail())
            ->from($this->fromEmail)
            ->to($this->adminEmail)
            ->replyTo($contact->getMail())
            ->subject('Nouvelle demande de contact : ' . $contact->getSujet())
            ->htmlTemplate('emails/contact_admin.html.twig')
            ->context([
                'contact' => $contact,
            ]);

        try {
            $this->mailer->send($email);
        } catch (TransportException $e) {
            // Keep the contact flow working even if the mail transport is unavailable.
        }
    }
}
