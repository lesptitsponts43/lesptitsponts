<?php

namespace App\Controller;

use App\Enum\StatutCommande;
use App\Repository\CommandeRepository;
use App\Service\SumUpService;
use App\Service\EmailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class WebhookController extends AbstractController
{
    #[Route('/webhook/sumup', name: 'sumup_webhook', methods: ['POST'])]
    public function sumupWebhook(
        Request $request,
        CommandeRepository $commandeRepository,
        SumUpService $sumUp,
        EntityManagerInterface $em,
        EmailService $emailService
    ): Response {
        $payload = json_decode((string) $request->getContent(), true);

        if (!is_array($payload)) {
            return new Response('Bad request', Response::HTTP_BAD_REQUEST);
        }

        $signature = $request->headers->get('x-sumup-signature')
            ?? $request->headers->get('X-Sumup-Signature')
            ?? $request->headers->get('X-SumUp-Signature')
            ?? null;

        $webhookSecret = $_ENV['SUMUP_WEBHOOK_SECRET'] ?? $_SERVER['SUMUP_WEBHOOK_SECRET'] ?? null;

        if ($webhookSecret && $signature) {
            $expectedSignature = strtolower(hash_hmac('sha256', (string) $request->getContent(), $webhookSecret));
            if (!hash_equals($expectedSignature, strtolower((string) $signature))) {
                return new Response('Unauthorized', Response::HTTP_UNAUTHORIZED);
            }
        }

        $checkoutId = $payload['id'] ?? $payload['checkout_id'] ?? $payload['data']['id'] ?? null;

        if (!$checkoutId) {
            return new Response('Bad request', Response::HTTP_BAD_REQUEST);
        }

        $commande = $commandeRepository->findOneBy(['sumupCheckoutId' => $checkoutId]);

        if (!$commande) {
            return new Response('Not found', Response::HTTP_NOT_FOUND);
        }

        $status = $this->resolveStatus($payload, $sumUp, $checkoutId);

        if ($status === StatutCommande::PAYEE) {
            if ($commande->getStatut() !== StatutCommande::PAYEE) {
                $commande->setStatut(StatutCommande::PAYEE);
                $commande->setModePaiement('SumUp');
                $em->flush();

                $emailService->sendPaymentConfirmation($commande);
                $emailService->sendAdminNewOrder($commande);
            }
        } elseif ($status === StatutCommande::ECHEC) {
            $commande->setStatut(StatutCommande::ECHEC);
        } elseif ($status === StatutCommande::REFUSE) {
            $commande->setStatut(StatutCommande::REFUSE);
        } elseif ($status === StatutCommande::ANNULEE) {
            $commande->setStatut(StatutCommande::ANNULEE);
        }

        if ($status !== StatutCommande::PAYEE) {
            $em->flush();
        }

        return new Response('OK', Response::HTTP_OK);
    }

    private function resolveStatus(array $payload, SumUpService $sumUp, string $checkoutId): string
    {
        $payloadStatus = strtoupper((string) ($payload['status'] ?? $payload['checkout']['status'] ?? $payload['data']['status'] ?? ''));

        if ($payloadStatus === 'PAID' || $payloadStatus === 'PAYMENT_ACCEPTED') {
            return StatutCommande::PAYEE;
        }

        if (in_array($payloadStatus, ['FAILED', 'FAILURE', 'DECLINED', 'DECLINED_PAYMENT', 'ERROR'], true)) {
            return StatutCommande::ECHEC;
        }

        if (in_array($payloadStatus, ['REJECTED', 'REFUSED', 'REJECTED_PAYMENT'], true)) {
            return StatutCommande::REFUSE;
        }

        if (in_array($payloadStatus, ['CANCELLED', 'CANCELED'], true)) {
            return StatutCommande::ANNULEE;
        }

        $apiStatus = strtoupper((string) $sumUp->getCheckoutStatus($checkoutId));

        if ($apiStatus === 'PAID' || $apiStatus === 'PAYMENT_ACCEPTED') {
            return StatutCommande::PAYEE;
        }

        if (in_array($apiStatus, ['FAILED', 'FAILURE', 'DECLINED', 'DECLINED_PAYMENT', 'ERROR'], true)) {
            return StatutCommande::ECHEC;
        }

        if (in_array($apiStatus, ['REJECTED', 'REFUSED', 'REJECTED_PAYMENT'], true)) {
            return StatutCommande::REFUSE;
        }

        if (in_array($apiStatus, ['CANCELLED', 'CANCELED'], true)) {
            return StatutCommande::ANNULEE;
        }

        return StatutCommande::A_PAYER;
    }
}