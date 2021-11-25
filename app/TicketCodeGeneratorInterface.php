<?php

namespace App;

interface TicketCodeGeneratorInterface
{
    public function generateFor($ticket);
}
