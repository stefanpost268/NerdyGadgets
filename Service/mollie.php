<?php

declare(strict_types=1);

namespace Service;

class Mollie {
    
    const MOLLIE_URL = "https://api.mollie.com/v2/";
    const MOLLIE_WEBHOOK_URL = "Webhooks/MollieWebhook.php";

    /**
     * Get payment data
     * 
     * @param string $description
     * @param float $price
     * @param float $shippingCost
     * @return array
     */
    private function paymentData(string $description, float $price, float $shippingCost, int $dbId): array
    {
        $appUrl = $_ENV["APP_URL"];
        return [
            "amount" => [
                "currency" => "EUR",
                "value" => strval(number_format($price + $shippingCost, 2, '.', ''))
            ],
            "description" => $description,
            "redirectUrl" => $appUrl . "status.php?id=" . $dbId,
            "webhookUrl" => $appUrl . self::MOLLIE_WEBHOOK_URL
        ];
    }

    /**
     * Create a payment at mollie
     * 
     * @param string $description
     * @param float $price
     * @param float $shippingCost
     * @param int $dbId
     * @return array
     */
    public function createPayment(string $description, float $price, float $shippingCost, int $dbId): array
    {
        return HTTP::post(
            self::MOLLIE_URL . "payments",
            [
                "Content-Type: application/json",
                "Authorization: Bearer " . $_ENV["MOLLIE_API_KEY"]
            ],
            $this->paymentData($description, $price, $shippingCost, $dbId)
        );
    }
}