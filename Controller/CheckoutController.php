<?php

declare(strict_types=1);

namespace Controller;

use mysqli;
use Service\Mollie;
use Service\product;
use Service\User;

class CheckoutController
{
    public Mollie $mollie;
    public User $user;
    public product $product;
    
    public function __construct(
        public mysqli $databaseConnection,
    ){
        $this->mollie = new Mollie();
        $this->user = new User();
        $this->product = new product();
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
        $databaseConnection->begin_transaction();

        $userId = $this->user->updateOrCreateUser($databaseConnection, $formData);

        $databaseId = $this->mollie->createTransaction($price, $shippingCost, $formData, $userId, $databaseConnection);
        if ($databaseId === 0) {
            throw new \Exception("Failed to create transaction");
        }

        $shoppingCart = $_SESSION["shoppingcart"] ?? [];

        $bindProducts = $this->product->bindProductsOnTransaction(
            $databaseId,
            $shoppingCart,
            $databaseConnection
        );

        if (!$bindProducts) {
            throw new \Exception("Failed to bind products to transaction");
        }

        foreach ($shoppingCart as $productId => $amount) {

            $status = $this->product->updateStockQuantityOnProduct($productId, (int) $amount, $databaseConnection);

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

        $databaseConnection->commit();

        header('Location: ' . $molliePayment["response"]->_links->checkout->href);
    }

    
}
