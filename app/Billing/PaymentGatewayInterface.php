<?php

namespace App\Billing;

interface PaymentGatewayInterface
{
    public function charge(int $amount, $tokent);

    public function getValidTestToken();

    public function newChargesDuring($callback);
}
