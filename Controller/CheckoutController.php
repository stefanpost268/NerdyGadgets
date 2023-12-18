<?php

declare(strict_types=1);

namespace Controller;

use mysqli;
use Service\Mollie;
use Service\User;

class CheckoutController
{
    public Mollie $mollie;
    public User $user;
    
    public function __construct(
        public mysqli $databaseConnection,
    ){
        $this->mollie = new Mollie();
        $this->user = new User();
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
        $userId = $this->user->updateOrCreateUser($databaseConnection, $formData);

        $databaseId = $this->mollie->createTransaction($price, $shippingCost, $formData, $userId, $databaseConnection);
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

        $molliePayment = $this->mollie->createPayment($description, $price, $shippingCost, $databaseId);

        if (!isset($molliePayment["response"]->id)) {
            throw new \Exception("Failed to create payment");
        }

        $status = $this->mollie->updateTransaction($databaseId, $molliePayment["response"]->id, $databaseConnection);

        if (!$status) {
            throw new \Exception("Failed to update database transaction");
        }

        header('Location: ' . $molliePayment["response"]->_links->checkout->href);
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
