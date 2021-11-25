<?php

namespace App\Facades;

use App\OrderConfirmationNumberGeneratorInterface;
use Illuminate\Support\Facades\Facade;

class OrderConfirmationNumber extends Facade
{
    protected static function getFacadeAccessor()
    {
        return OrderConfirmationNumberGeneratorInterface::class;
    }
}
