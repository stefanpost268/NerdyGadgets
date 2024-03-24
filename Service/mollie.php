<?php

declare(strict_types=1);

namespace Service;

use mysqli;

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
        if($_SERVER['SERVER_NAME'] == "localhost") throw new \Exception("Transacties werken niet op localhost.");
        if(!isset($_ENV["MOLLIE_API_KEY"]) || $_ENV["MOLLIE_API_KEY"] === "") throw new \Exception("Mollie is niet ingesteld.");
        
        return HTTP::post(
            self::MOLLIE_URL . "payments",
            [
                "Content-Type: application/json",
                "Authorization: Bearer " . $_ENV["MOLLIE_API_KEY"]
            ],
            $this->paymentData($description, $price, $shippingCost, $dbId)
        );
    }

    /**
     * Create transaction in database.
     * 
     * @param string $description
     * @param float $price
     * @param float $shippingCost
     * @param array $formData
     * @param mysqli $databaseConnection
     * @return int returns the last inserted ID and 0 when failed.
     */
    public function createTransaction(float $price, float $shippingCost, array $formData, int $userId, mysqli $databaseConnection): int
    {
        $totalCost = $price + $shippingCost;

        $query = "INSERT INTO `Transaction` (
            `userId`,
            `status`,
            `payment`,
            `postalcode`,
            `housenr`,
            `residence`
        ) 
        VALUES (?,'open',?,?,?,?);";

        $statement = mysqli_prepare($databaseConnection, $query);

        mysqli_stmt_bind_param($statement, 'idsss', $userId, $totalCost, $formData['postalcode'], $formData['housenr'], $formData['residence']);

        $success = mysqli_stmt_execute($statement);
        $insertedId = ($success) ? mysqli_insert_id($databaseConnection) : 0;
        mysqli_stmt_close($statement);

        return $insertedId;
    }

    /**
     * Update transaction in database.
     * 
     * @param int $databaseId
     * @param string $mollieId
     * @param mysqli $databaseConnection
     * @return bool
     */
    public function updateTransaction(int $databaseId, string $mollieId, mysqli $databaseConnection): bool
    {
        $query = "UPDATE `Transaction` SET `transaction_id` = ? WHERE `id` = ?";

        $statement = mysqli_prepare($databaseConnection, $query);
        mysqli_stmt_bind_param($statement, 'ss', $mollieId, $databaseId);
        $success = mysqli_stmt_execute($statement);
        mysqli_stmt_close($statement);

        return $success;
    }
}