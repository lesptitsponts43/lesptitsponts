<?php

namespace App\Service;

use SumUp\SumUp;

class SumUpService
{
    private SumUp $client;
    private string $merchantCode;

    public function __construct(string $sumupApiKey, string $merchantCode)
    {
        $this->client = new SumUp(['api_key' => $sumupApiKey]);
        $this->merchantCode = $merchantCode;
    }

    public function getCheckoutStatus(string $checkoutId): string
    {
        return $this->client->checkouts()->get($checkoutId)->status;
    }

    public function createHostedCheckout(string $reference, float $amount, string $currency, string $redirectUrl, ?string $returnUrl = null): array
    {
        $checkout = $this->client->checkouts()->create([
            'merchant_code'      => $this->merchantCode,
            'amount'             => $amount,
            'currency'           => $currency,
            'checkout_reference' => $reference,
            'description'        => 'Commande ' . $reference,
            'redirect_url'       => $redirectUrl,
            'return_url'         => $returnUrl ?? $redirectUrl,
            'hosted_checkout'    => ['enabled' => true],
        ]);

        return [
            'id'  => $checkout->id,
            'url' => $checkout->hostedCheckoutUrl,
        ];
    }
}