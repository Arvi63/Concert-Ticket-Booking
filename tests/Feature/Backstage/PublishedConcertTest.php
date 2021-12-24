<?php
namespace Tests\Feature\Backstage;

use App\Models\Concert;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class PublishedConcertTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function promoter_can_publish_their_own_concert()
    {
        $user = User::factory()->create();
        $concert = Concert::factory()->unpublished()->create([
            'user_id' => $user->id,
            'ticket_quantity' => 3,
        ]);

        $response = $this->actingAs($user)->post('/backstage/published-concerts', [
            'concert_id' => $concert->id
        ]);

        $response->assertRedirect(('/backstage/concerts'));
        $concert = $concert->fresh();
        $this->assertTrue($concert->isPublished());
        $this->assertEquals(3, $concert->ticketsRemaining());
    }

    /** @test */
    public function concert_can_only_be_published_once()
    {
        $this->withExceptionHandling();

        $user = User::factory()->create();
        $concert = Concert::factory()->createPublished([
            'user_id' => $user->id,
            'ticket_quantity' => 3,
        ]);

        $response = $this->actingAs($user)->post('/backstage/published-concerts', [
            'concert_id' => $concert->id
        ]);

        $response->assertStatus(422);
        $this->assertEquals(3, $concert->fresh()->ticketsRemaining());
    }
}
