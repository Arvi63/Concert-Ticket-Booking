<?php

namespace App;

class HashidsTicketCodeGenerator implements TicketCodeGeneratorInterface
{
    private $hashids;

    public function __construct($salt)
    {
        $this->hashids = new \Hashids\Hashids($salt = $salt, $minHashLength = 6, $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ');
    }

    public function generateFor($ticket)
    {
        return $this->hashids->encode($ticket->id);
    }
}
