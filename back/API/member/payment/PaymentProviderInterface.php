<?php

interface PaymentProviderInterface
{
    public function createPayment(float $amount, string $currency, string $description, int $idAbonnement, string $subscriptionBegin, int $id_box): array;

    public function verifyPayment(string $paymentId): bool;
}
