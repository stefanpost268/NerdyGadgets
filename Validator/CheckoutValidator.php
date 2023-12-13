<?php

declare(strict_types=1);

namespace Validator;

class CheckoutValidator
{
    public function validate(array $products): array {
        return [
            ...$this->productStockAvailable($products)
        ];
    }

    public function productStockAvailable(array $products): array {
        $shoppingCartItems = $_SESSION["shoppingcart"];
        $errors = [];
        
        foreach($shoppingCartItems as $productId => $amount) {
            $productData = [];
            foreach($products as $product) {
                if($product["item"]["StockItemID"] == $productId) {
                    $productData = $product;
                    break;
                }
            }

            if($productData["item"]["QuantityOnHand"] < $amount) {
                $errors[] = "Product {$product["item"]["StockItemName"]} is niet meer op voorraad";
            }
        }
        
        return $errors;
    }
}