<?php

require_once __DIR__ . '/../../../../config/app.php';

class PayPalProvider
{
    private $clientId;
    private $secret;
    private $apiUrl;

    public function __construct()
    {
        // Infos du tableau de bord Paypal (mode sandbox ou live)
        $this->clientId = APP_CONFIG['paypal_client_id'];
        $this->secret = APP_CONFIG['paypal_secret'];

        // URL API Paypal (sandbox ou live)
        $this->apiUrl = "https://api-m.sandbox.paypal.com";
    }

    // Fonction pour récupérer un token d’accès
    private function getAccessToken()
    {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $this->apiUrl . "/v1/oauth2/token",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_USERPWD => $this->clientId . ":" . $this->secret,
            CURLOPT_POSTFIELDS => "grant_type=client_credentials"
        ]);

        $response = json_decode(curl_exec($curl), true);
        curl_close($curl);

        return $response["access_token"];
    }

    public function createPayment($amount, $description)
    {
        $token = $this->getAccessToken();

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $this->apiUrl . "/v2/checkout/orders",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => [
                "Content-Type: application/json",
                "Authorization: Bearer $token"
            ],
            CURLOPT_POSTFIELDS => json_encode([
                "intent" => "CAPTURE",
                "purchase_units" => [[
                    "amount" => [
                        "currency_code" => "EUR",
                        "value" => number_format($amount, 2, ".", "")
                    ],
                    "description" => $description
                ]],
                "application_context" => [
                    "return_url" => APP_BASE_URL . "/index.php?p=validPayment",
                    "cancel_url" => APP_BASE_URL . "/index.php?p=dashboard"
                ]
            ])
        ]);

        $response = json_decode(curl_exec($curl), true);
        curl_close($curl);

        // Paypal renvoie plusieurs liens, dont celui de redirection
        $approvalLink = null;
        foreach ($response["links"] as $link) {
            if ($link["rel"] === "approve") {
                $approvalLink = $link["href"];
            }
        }

        return [
            "paymentId" => $response["id"],
            "redirectUrl" => $approvalLink
        ];
    }
}
