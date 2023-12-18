<?php

declare(strict_types=1);

namespace Controller;

use mysqli;
use Service\HTTP;
use Service\Mollie;

class CheckoutController
{
    public Mollie $mollie;
    
    public function __construct(
        public mysqli $databaseConnection,
    ){
        $this->mollie = new Mollie();
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
    private function createPayment(string $description, float $price, float $shippingCost, int $dbId): array
    {
        return HTTP::post(
            Mollie::MOLLIE_URL . "payments",
            [
                "Content-Type: application/json",
                "Authorization: Bearer " . $_ENV["MOLLIE_API_KEY"]
            ],
            $this->mollie->paymentData($description, $price, $shippingCost, $dbId)
        );
    }

    /**
     * Create a transaction in the database and a payment at mollie.
     * 
     * @param string $description
     * @param array $formData
     * @param float $price
     * @param float $shippingCost
     * @param mysqli $databaseConnection
     * @return void
     */
    public function getTransaction(string $description, array $formData, float $price, float $shippingCost, mysqli $databaseConnection): void
    {
        $userId = $this->updateOrCreateUser($databaseConnection, $formData);

        die(var_dump($userId));
        $databaseId = $this->createTransaction($price, $shippingCost, $formData, $userId, $databaseConnection);
        if ($databaseId === 0) {
            throw new \Exception("Failed to create transaction");
        }

        $shoppingCart = $_SESSION["shoppingcart"] ?? [];

        $bindProducts = $this->bindProductsOnTransaction(
            $databaseId,
            $shoppingCart,
            $databaseConnection
        );

        if (!$bindProducts) {
            throw new \Exception("Failed to bind products to transaction");
        }

        foreach ($shoppingCart as $productId => $amount) {

            $status = $this->updateStockQuantityOnProduct($productId, (int) $amount, $databaseConnection);

            if (!$status) {
                throw new \Exception("Failed to update stock price");
            }
        }

        $molliePayment = $this->createPayment($description, $price, $shippingCost, $databaseId);

        if (!isset($molliePayment["response"]->id)) {
            throw new \Exception("Failed to create payment");
        }

        $status = $this->updateTransaction($databaseId, $molliePayment["response"]->id, $databaseConnection);

        if (!$status) {
            throw new \Exception("Failed to update database transaction");
        }

        header('Location: ' . $molliePayment["response"]->_links->checkout->href);
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
        $query = "UPDATE `Transaction` SET `transaction_id` = ? WHERE `id` = ?";

        $statement = mysqli_prepare($databaseConnection, $query);
        mysqli_stmt_bind_param($statement, 'ss', $mollieId, $databaseId);
        $success = mysqli_stmt_execute($statement);
        mysqli_stmt_close($statement);

        return $success;
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
    private function createTransaction(float $price, float $shippingCost, array $formData, int $userId, mysqli $databaseConnection): int
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
     * Update or create user in database.
     * 
     * @param mysqli $databaseConnection
     * @param array $formData
     * @return int returns id of user.
     */
    private function updateOrCreateUser(mysqli $databaseConnection, array $userData): int
    {
        $query = "INSERT INTO `User` (`email`,`name`) VALUES (?, ?) 
            ON DUPLICATE KEY UPDATE 
            `email` = VALUES(`email`),
            `name` = VALUES(`name`),
            `id` = LAST_INSERT_ID(`id`);
        ";

        $statement = mysqli_prepare($databaseConnection, $query);

        mysqli_stmt_bind_param($statement, 'ss', $userData['email'], $userData['name']);

        $success = mysqli_stmt_execute($statement);
        $insertedOrUpdatedId = ($success) ? mysqli_insert_id($databaseConnection) : 0;
        mysqli_stmt_close($statement);

        return $insertedOrUpdatedId;
    }

    /**
     * Bind products to transaction in database.
     * 
     * @param int $databaseId
     * @param array $shoppingCart
     * @param mysqli $databaseConnection
     * @return bool
     */
    private function bindProductsOnTransaction(int $databaseId, array $shoppingCart, $databaseConnection): bool
    {
        $query = "INSERT INTO `TransactionBind` (
            `transactionId`,
            `stockitemId`,
            `amount`
        ) VALUES (?,?,?);
        ";

        $statement = mysqli_prepare($databaseConnection, $query);

        foreach ($shoppingCart as $productId => $amount) {
            mysqli_stmt_bind_param($statement, 'iii', $databaseId, $productId, $amount);
            mysqli_stmt_execute($statement);
        }

        mysqli_stmt_close($statement);

        return true;
    }

    /**
     * Removes items from stock of a product.
     * 
     * @param int $productId
     * @param int $amount
     * @param mysqli $databaseConnection
     * @return bool
     */
    private function updateStockQuantityOnProduct(int $productId, int $amount, mysqli $databaseConnection): bool
    {
        $query = "UPDATE `stockitemholdings` SET `QuantityOnHand` = `QuantityOnHand` - ? WHERE `StockItemID` = ?;";

        $statement = mysqli_prepare($databaseConnection, $query);
        mysqli_stmt_bind_param($statement, 'ii', $amount, $productId);
        $success = mysqli_stmt_execute($statement);
        mysqli_stmt_close($statement);

        return $success;
    }
}
