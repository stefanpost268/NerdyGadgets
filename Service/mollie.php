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
    public function paymentData(string $description, float $price, float $shippingCost, int $dbId): array
    {
        $appUrl = $_ENV["APP_URL"];
        return [
            "amount" => [
                "currency" => "EUR",
                "value" => strval(number_format($price + $shippingCost, 2, '.', ''))
            ],
            "description" => $description,
            "redirectUrl" => $appUrl . "status.php?id=" . $dbId,
            "webhookUrl" => $appUrl . Mollie::MOLLIE_WEBHOOK_URL
        ];
    }
}