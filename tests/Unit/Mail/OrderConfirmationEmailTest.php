<?php

namespace Tests\Unit\Mail;

use App\Mail\OrderConfirmationEmail;
use App\Models\Order;
use PHPUnit\Framework\TestCase;

class OrderConfirmationEmailTest extends TestCase
{
    /** @test */
    public function email_contains_a_links_to_the_order_confirmation_page()
    {
        $order = Order::factory()->make([
            'confirmation_number' => 'ORDERCONFIRMATION1234',
        ]);

        $email = new OrderConfirmationEmail($order);

        $rendered = $email->render();
        // $rendered = $this->render($email);

        $this->assertContains(url('/orders/ORDERCONFIRMATION1234'), $rendered);
    }

    /** @test */
    public function email_has_a_subject()
    {
        $order = Order::factory()->make();

        $email = new OrderConfirmationEmail($order);

        $this->assertEquals('Your TicketBeast order', $email->build()->subject);
    }

    // private function render($mailable)
    // {
    //     $mailable->build();

    //     return view($mailable->view, $mailable->buildViewData())->render();
    // }
}
