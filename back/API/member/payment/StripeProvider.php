<?php

require_once __DIR__ . '/PaymentProviderInterface.php';
require_once __DIR__ . '/stripe/init.php'; // si on utilise Stripe SDK
require_once __DIR__ . '/../../../../config/app.php';

class StripeProvider implements PaymentProviderInterface
{
    private \Stripe\StripeClient $stripe;

    public function __construct(private int $userId)
    {
        if (!APP_CONFIG['stripe_secret_key']) {
            throw new RuntimeException('Stripe non configure.');
        }
        $this->stripe = new \Stripe\StripeClient(APP_CONFIG['stripe_secret_key']);
    }

    public function createPayment(
        float $amount,
        string $currency,
        string $description,
        int $idAbonnement,
        string $subscriptionBegin,
        int $id_box
    ): array {
        $session = $this->stripe->checkout->sessions->create([
            'payment_method_types' => ['card'],
            'mode' => 'payment',
            'line_items' => [[
                'price_data' => [
                    'currency' => $currency,
                    'unit_amount' => (int) round($amount * 100),
                    'product_data' => [
                        'name' => $description
                    ]
                ],
                'quantity' => 1
            ]],
            'client_reference_id' => (string) $this->userId,
            'metadata' => [
                'id_abonnement' => $idAbonnement,
                'subscription_begin' => $subscriptionBegin,
                'id_box' => $id_box
            ],
            'success_url' => APP_BASE_URL . "/index.php?p=validPayment&method=Stripe&pid={CHECKOUT_SESSION_ID}",
            'cancel_url' => APP_BASE_URL . '/index.php?p=dashboard'
        ]);

        return [
            "paymentId" => $session->id,
            "redirectUrl" => $session->url
        ];
    }

    public function getSession(string $paymentId): \Stripe\Checkout\Session
    {
        return $this->stripe->checkout->sessions->retrieve($paymentId);
    }

    public function verifyPayment(string $paymentId): bool
    {
        $session = $this->getSession($paymentId);

        return $session->payment_status === "paid";
    }
}
