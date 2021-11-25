<?php

namespace App\Facades;

use App\TicketCodeGeneratorInterface;
use Illuminate\Support\Facades\Facade;

class TicketCode extends Facade
{
    protected static function getFacadeAccessor()
    {
        return TicketCodeGeneratorInterface::class;
    }

    protected static function getMockableClass()
    {
        return static::getFacadeAccessor();
    }
}
