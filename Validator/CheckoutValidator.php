<?php

declare(strict_types=1);

namespace Validator;

class CheckoutValidator
{
    public function validate(array $formData, array $products): array {
        return [
            ...$this->productStockAvailable($products),
            ...$this->validateEmail($formData),
            ...$this->validateHouseNumer($formData),
            ...$this->validatePostalCode($formData)
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

    private function validateEmail(array $formData): array {
        $errors = [];
        if(!filter_var($formData["email"], FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Email is niet geldig";
        }
        return $errors;
    }

    private function validateHouseNumer(array $formData): array {
        $errors = [];
        if(!isset($formData["housenr"]) || $formData["housenr"] == "") {
            $errors[] = "Huisnummer is verplicht";
        } else if(!preg_match("/^\s*\d{1,3}\s*[A-Za-z]{0,1}\s*$/", $formData["housenr"])) {
            $errors[] = "Huisnummer is niet geldig";
        }
        return $errors;
    }

    public function validatePostalCode(array $formData): array {
        $errors = [];
        if(!isset($formData["postalcode"]) || $formData["postalcode"] == "") {
            $errors[] = 'Postcode is verplicht.';
        } else if (!preg_match('/^\d{4} [A-Za-z]{2}$/', $formData["postalcode"])) {
            $errors[] = "Voer een gelde postcode in. (Bijvoorbeeld: 8302 BB)";
        }

        return $errors;
    }
}