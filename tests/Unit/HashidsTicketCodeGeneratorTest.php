<?php

namespace Tests\Unit;

use App\HashidsTicketCodeGenerator;
use App\Models\Ticket;
use PHPUnit\Framework\TestCase;

class HashidsTicketCodeGeneratorTest extends TestCase
{
    /** @test */
    public function ticket_codes_are_at_least_6_characters_long()
    {
        $ticketGenerator = new HashidsTicketCodeGenerator('testsalt1');

        $code = $ticketGenerator->generateFor(new Ticket(['id' => 1]));

        $this->assertTrue(strlen($code) >= 6);
    }

    /** @test */
    public function ticket_codes_can_only_contain_uppercase_letters()
    {
        $ticketGenerator = new HashidsTicketCodeGenerator('testsalt1');

        $code = $ticketGenerator->generateFor(new Ticket(['id' => 1]));

        $this->assertMatchesRegularExpression('/^[A-Z]+$/', $code);
    }

    /** @test */
    public function ticket_codes_for_the_same_ticket_id_are_the_same()
    {
        $ticketGenerator = new HashidsTicketCodeGenerator('testsalt1');

        $code1 = $ticketGenerator->generateFor(new Ticket(['id' => 1]));
        $code2 = $ticketGenerator->generateFor(new Ticket(['id' => 1]));

        $this->assertEquals($code1, $code2);
    }

    /** @test */
    public function ticket_codes_for_different_ticket_ids_are_different()
    {
        $ticketGenerator = new HashidsTicketCodeGenerator('testsalt1');

        $code1 = $ticketGenerator->generateFor(new Ticket(['id' => 1]));
        $code2 = $ticketGenerator->generateFor(new Ticket(['id' => 2]));

        $this->assertNotEquals($code1, $code2);
    }

    /** @test */
    public function ticket_codes_generated_with_different_salts_are_different()
    {
        $ticketGenerator1 = new HashidsTicketCodeGenerator('testsalt1');
        $ticketGenerator2 = new HashidsTicketCodeGenerator('testsalt2');

        $code1 = $ticketGenerator1->generateFor(new Ticket(['id' => 1]));
        $code2 = $ticketGenerator2->generateFor(new Ticket(['id' => 1]));

        $this->assertNotEquals($code1, $code2);
    }
}
