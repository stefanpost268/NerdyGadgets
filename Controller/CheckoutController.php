<?php

declare(strict_types=1);

namespace Controller;

class CheckoutController
{
    public $databaseConnection;

    public function createCheckout(): void
    {
        var_dump($this->databaseConnection);
    }
}