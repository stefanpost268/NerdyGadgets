<?php

declare(strict_types=1);

namespace Validator;

class CheckoutValidator
{
    public function validate(array $formData, array $products): array {
        return [
            ...$this->productStockAvailable($products),
            ...$this->validateEmail($formData),
            ...$this->validateHouseNumer($formData)
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

    private function validateEmail(): array {
        $errors = [];
        if(!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Email is niet geldig";
        }
        return $errors;
    }

    private function validateHouseNumer(): array {
        $errors = [];
        if(!isset($_POST["housenr"]) || $_POST["housenr"] == "") {
            $errors[] = "Huisnummer is verplicht";
        } else if(!preg_match("/^\s*\d{1,3}\s*[A-Za-z]{0,1}\s*$/", $_POST["housenr"])) {
            $errors[] = "Huisnummer is niet geldig";
        }
        return $errors;
    }
}