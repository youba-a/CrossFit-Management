<?php

class PaymentService
{
    private PaymentProviderInterface $provider;

    public function __construct(PaymentProviderInterface $provider)
    {
        $this->provider = $provider;
    }

    public function pay(float $amount, string $description, $idAbonnement, string $subscriptionBegin, int $id_box): array
    {
        return $this->provider->createPayment($amount, "EUR", $description, $idAbonnement, $subscriptionBegin, $id_box);
    }

    public function confirm(string $paymentId): bool
    {
        return $this->provider->verifyPayment($paymentId);
    }
}
