<?php

declare(strict_types=1);

namespace Controller;

require_once __DIR__ . "/../Service/http.php";

use mysqli;
use Service\HTTP;

class CheckoutController
{
    CONST MOLLIE_URL = "https://api.mollie.com/v2/";
    public $databaseConnection;

    /**
     * Get payment data
     * 
     * @param string $description
     * @param float $price
     * @return array
     */
    private function paymentData(string $description, float $price, int $dbId): array {
        $appUrl = $_ENV["APP_URL"];

        return [
            "amount" => [
                "currency" => "EUR",
                "value" => strval(number_format($price, 2, '.', ''))
            ],
            "description" => $description,
            "redirectUrl" => $appUrl . "status.php?id=".$dbId,
            "webhookUrl" => $appUrl . "/webhooks/MollieWebhook.php"
        ];
    }

    /**
     * Create a payment at mollie
     * 
     * @param array $selectedProducts
     * @param string $description
     * @return array
     */
    private function createPayment(string $description, float $price, int $dbId): array
    {
        return HTTP::post(self::MOLLIE_URL . "payments", [
                "Content-Type: application/json",
                "Authorization: Bearer ".$_ENV["MOLLIE_API_KEY"]
            ],
            $this->paymentData($description, $price, $dbId)
        );
    }

    /**
     * Create a transaction in the database and a payment at mollie.
     * 
     * @param string $description
     * @param float $price
     * @param mysqli $databaseConnection
     * @return void
     */
    public function getTransaction(string $description, float $price, mysqli $databaseConnection): void
    {
        
        $databaseId = $this->createTransaction($price, $databaseConnection);

        if($databaseId === 0) {
            throw new \Exception("Failed to create transaction");
        }

        $molliePayment = $this->createPayment($description, $price, $databaseId);

        if(!isset($molliePayment["response"]->id)) {
            throw new \Exception("Failed to create payment");
        }

        $status = $this->updateTransaction($databaseId, $molliePayment["response"]->id, $databaseConnection);

        if(!$status) {
            throw new \Exception("Failed to update database transaction");
        }
        
        header('Location: '.$molliePayment["response"]->_links->checkout->href);
    }

    /**
     * Update transaction in database.
     * 
     * @param int $databaseId
     * @param string $mollieId
     * @param mysqli $databaseConnection
     * @return bool
     */
    private function updateTransaction(int $databaseId, string $mollieId, mysqli $databaseConnection): bool
    {
        $query = "UPDATE `Transaction` SET `transaction_id` = '$mollieId' WHERE `id` = $databaseId;";

        $statement = mysqli_prepare($databaseConnection, $query);
        $success = mysqli_stmt_execute($statement);
        mysqli_stmt_close($statement);

        return $success;
    }

    /**
     * Create transaction in database.
     * 
     * @param string $description
     * @param float $price
     * @param mysqli $databaseConnection
     * @return int returns the last inserted ID and 0 when failed.
     */
    private function createTransaction(float $price, mysqli $databaseConnection): int
    {
        $query = "INSERT INTO `Transaction` (`status`, `payment`) VALUES ('open', $price);";

        $statement = mysqli_prepare($databaseConnection, $query);
        $success = mysqli_stmt_execute($statement);
        $insertedId = ($success) ? mysqli_insert_id($databaseConnection) : 0;
        mysqli_stmt_close($statement);

        return $insertedId;
    }

}