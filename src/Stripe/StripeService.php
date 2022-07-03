<?php

namespace App\Stripe;

use Stripe\Stripe;
use App\Entity\Purchase;
use Stripe\PaymentIntent;

class StripeService
{
    protected string $secretKey;
    protected string $publicKey;

    public function __construct(string $secretKey, string $publicKey)
    {
        $this->secretKey = $secretKey;
        $this->publicKey = $publicKey;
    }

    public function getPublicKey(): string
    {
        return $this->publicKey;
    }

    public function getPaymentIntent(Purchase $purchase): PaymentIntent
    {
        Stripe::setApiKey('sk_test_51KxBiALVKfR0y6C4drUFYHZHHzlsmQRyqZDQp92uVIpI0Xn6AFxxzFavWkDgB8NRvEzZK3mZK1mOglEkIk6MZII700i2M5RntJ');
        $intent = PaymentIntent::create([
            'amount' => $purchase->getTotal(),
            'currency' => 'eur'

        ]);

        return $intent;
    }
}
