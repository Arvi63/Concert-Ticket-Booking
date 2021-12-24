<?php
namespace Database\Factories;

use App\Models\Order;
use App\Models\Ticket;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'email' => 'somebody@example.com',
            'amount' => 3000,
            'confirmation_number' => 'ORDERCONFIRMATION1234',
            'card_last_four' => '4242',
        ];
    }

    public static function createForConcert($concert, $overrides = [], $ticketQuantity = 1)
    {
        $order = Order::factory()->create($overrides);

        $tickets = Ticket::factory($ticketQuantity)->create(['concert_id' => $concert->id]);

        $order->tickets()->saveMany($tickets);

        return $order;
    }
}
