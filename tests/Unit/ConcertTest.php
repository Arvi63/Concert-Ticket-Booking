<?php
namespace Tests\Unit;

use App\Exceptions\NotEnoughTicketsException;
use App\Models\Concert;
use App\Models\Order;
use App\Models\Ticket;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class ConcertTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function can_get_formatted_date()
    {
        $concert = Concert::factory()->make([
            'date' => Carbon::parse('2016-12-01 8:00pm'),
        ]);

        $this->assertEquals('December 1, 2016', $concert->formatted_date);
    }

    /** @test */
    public function can_get_formatted_start_time()
    {
        $concert = Concert::factory()->make([
            'date' => Carbon::parse('2016-12-01 17:00:00'),
        ]);

        $this->assertEquals('5:00pm', $concert->formatted_start_time);
    }

    /** @test */
    public function can_get_ticket_price_in_dollars()
    {
        $concert = Concert::factory()->make([
            'ticket_price' => 6750,
        ]);

        $this->assertEquals('67.50', $concert->ticket_price_in_dollars);
    }

    /** @test */
    public function concerts_with_a_published_at_date_are_published()
    {
        $publishedConcertA = Concert::factory()->create(['published_at' => Carbon::parse('-1 weeek')]);
        $publishedConcertB = Concert::factory()->create(['published_at' => Carbon::parse('-1 weeek')]);
        $unpublishedConcert = Concert::factory()->create(['published_at' => null]);

        $publishedConcerts = Concert::published()->get();

        $this->assertTrue($publishedConcerts->contains($publishedConcertA));
        $this->assertTrue($publishedConcerts->contains($publishedConcertB));
        $this->assertFalse($publishedConcerts->contains($unpublishedConcert));
    }

    /** @test */
    public function concerts_can_be_published()
    {
        $concert = Concert::factory()->create([
            'published_at' => null,
            'ticket_quantity' => 5,
        ]);
        $this->assertFalse($concert->isPublished());
        $this->assertEquals(0, $concert->ticketsRemaining());

        $concert->publish();

        $this->assertTrue($concert->isPublished());
        $this->assertEquals(5, $concert->ticketsRemaining());
    }

    /** @test */
    public function can_add_tickets()
    {
        $concert = Concert::factory()->create();

        $concert->addTickets(50);

        $this->assertEquals(50, $concert->ticketsRemaining());
    }

    /** @test */
    public function tickets_remaining_does_not_include_tickets_associated_with_an_order()
    {
        $concert = Concert::factory()->create();

        $concert->tickets()->saveMany(Ticket::factory(3)->create(['order_id' => 1]));
        $concert->tickets()->saveMany(Ticket::factory(2)->create(['order_id' => null]));

        $this->assertEquals(2, $concert->ticketsRemaining());
    }

    /** @test */
    public function tickets_sold_only_includes_tickets_associated_with_an_order()
    {
        $concert = Concert::factory()->create();

        $concert->tickets()->saveMany(Ticket::factory(3)->create(['order_id' => 1]));
        $concert->tickets()->saveMany(Ticket::factory(2)->create(['order_id' => null]));

        $this->assertEquals(3, $concert->ticketsSold());
    }

    /** @test */
    public function total_tickets_includes_all_tickets()
    {
        $concert = Concert::factory()->create();

        $concert->tickets()->saveMany(Ticket::factory(3)->create(['order_id' => 1]));
        $concert->tickets()->saveMany(Ticket::factory(2)->create(['order_id' => null]));

        $this->assertEquals(5, $concert->totalTickets());
    }

    /** @test */
    public function calculating_the_percentage_of_tickets_sold()
    {
        $concert = Concert::factory()->create();

        $concert->tickets()->saveMany(Ticket::factory(2)->create(['order_id' => 1]));
        $concert->tickets()->saveMany(Ticket::factory(5)->create(['order_id' => null]));

        $this->assertEquals(28.57, $concert->percentSoldOut());
    }

    /** @test */
    public function calculating_revenue_in_dollars()
    {
        $concert = Concert::factory()->create();
        $orderA = Order::factory()->create(['amount' => 3850]);
        $orderB = Order::factory()->create(['amount' => 9625]);

        $concert->tickets()->saveMany(Ticket::factory(2)->create(['order_id' => $orderA->id]));
        $concert->tickets()->saveMany(Ticket::factory(5)->create(['order_id' => $orderB->id]));

        $this->assertEquals(134.75, $concert->revenueInDollars());
    }

    /** @test */
    public function trying_to_reserve_more_tickets_than_remain_throws_an_exception()
    {
        $concert = Concert::factory()->create();
        $concert->addTickets(10);

        try {
            $reservation = $concert->reserveTickets(11, 'john@example.com');
        } catch (NotEnoughTicketsException $e) {
            $this->assertFalse($concert->hasOrderFor('jane@example.com'));
            $this->assertEquals(10, $concert->ticketsRemaining());

            return;
        }

        $this->fail('Order Succeeded even though there were not enough tickets remaining');
    }

    /** @test */
    public function can_reserve_available_tickets()
    {
        $concert = Concert::factory()->create()->addTickets(3);
        $this->assertEquals(3, $concert->ticketsRemaining());

        $reservation = $concert->reserveTickets(2, 'john@example.com');

        $this->assertCount(2, $reservation->tickets());
        $this->assertEquals('john@example.com', $reservation->email());
        $this->assertEquals(1, $concert->ticketsRemaining());
    }

    /** @test */
    public function cannot_reserve_tickets_that_have_already_been_purchased()
    {
        $concert = Concert::factory()->create()->addTickets(3);
        $order = Order::factory()->create();
        $order->tickets()->saveMany($concert->tickets->take(2));

        try {
            $concert->reserveTickets(2, 'john@example.com');
        } catch (NotEnoughTicketsException $e) {
            $this->assertEquals(1, $concert->ticketsRemaining());

            return;
        }

        $this->fail('Reserving tickets succeeded even though the tickets were already sold');
    }

    /** @test */
    public function cannot_reserve_tickets_that_have_already_been_reserved()
    {
        $concert = Concert::factory()->create()->addTickets(3);
        $concert->reserveTickets(2, 'jane@example.com');

        try {
            $concert->reserveTickets(2, 'john@example.com');
        } catch (NotEnoughTicketsException $e) {
            $this->assertEquals(1, $concert->ticketsRemaining());

            return;
        }

        $this->fail('Reserving tickets succeeded even though the tickets were already reserved');
    }
}
