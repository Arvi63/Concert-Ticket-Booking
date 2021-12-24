<?php
namespace Tests\Feature\Backstage;

use App\Models\Concert;
use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class ViewPublishedConcertOrdersTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function a_promoter_can_view_the_orders_of_their_own_published_concert()
    {
        $user = User::factory()->create();
        $concert = Concert::factory()->createPublished(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get("/backstage/published-concerts/{$concert->id}/orders");

        $response->assertStatus(200);
        $response->assertViewIs('backstage.published-concert-orders.index');
        $this->assertTrue($response->data('concert')->is($concert));
    }

    /** @test */
    public function a_promoter_can_view_the_10_most_recent_orders_for_their_concert()
    {
        $user = User::factory()->create();
        $concert = Concert::factory()->createPublished(['user_id' => $user->id]);

        $oldOrder = Order::factory()->createForConcert($concert, ['created_at' => Carbon::parse('11 days ago')]);
        $recentOrder1 = Order::factory()->createForConcert($concert, ['created_at' => Carbon::parse('10 days ago')]);
        $recentOrder2 = Order::factory()->createForConcert($concert, ['created_at' => Carbon::parse('9 days ago')]);
        $recentOrder3 = Order::factory()->createForConcert($concert, ['created_at' => Carbon::parse('8 days ago')]);
        $recentOrder4 = Order::factory()->createForConcert($concert, ['created_at' => Carbon::parse('7 days ago')]);
        $recentOrder5 = Order::factory()->createForConcert($concert, ['created_at' => Carbon::parse('6 days ago')]);
        $recentOrder6 = Order::factory()->createForConcert($concert, ['created_at' => Carbon::parse('5 days ago')]);
        $recentOrder7 = Order::factory()->createForConcert($concert, ['created_at' => Carbon::parse('4 days ago')]);
        $recentOrder8 = Order::factory()->createForConcert($concert, ['created_at' => Carbon::parse('3 days ago')]);
        $recentOrder9 = Order::factory()->createForConcert($concert, ['created_at' => Carbon::parse('2 days ago')]);
        $recentOrder10 = Order::factory()->createForConcert($concert, ['created_at' => Carbon::parse('1 days ago')]);

        $response = $this->actingAs($user)->get("/backstage/published-concerts/{$concert->id}/orders");

        $response->data('orders')->assertEquals([
            $recentOrder10,
            $recentOrder9,
            $recentOrder8,
            $recentOrder7,
            $recentOrder6,
            $recentOrder5,
            $recentOrder4,
            $recentOrder3,
            $recentOrder2,
            $recentOrder1,
        ]);

        $response->data('orders')->assertNotContains($oldOrder);
    }
}
