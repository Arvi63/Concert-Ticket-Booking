<?php

namespace Tests\Feature\Backstage;

use App\Models\Concert;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Testing\Assert;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class ViewConcertListTest extends TestCase
{
    use DatabaseMigrations;

    public function setUp(): void
    {
        parent::setUP();

        TestResponse::macro('data', function ($key) {
            return $this->original->getData()[$key];
        });

        Collection::macro('assertContains', function ($value) {
            return Assert::assertTrue($this->contains($value), 'Failed asserting that the collection contained the specified value');
        });

        Collection::macro('assertNotContains', function ($value) {
            return Assert::assertFalse($this->contains($value), 'Failed asserting that the collection did not contain the specified value');
        });
    }

    /** @test */
    public function guests_cannot_view_a_promoters_concert_list()
    {
        $response = $this->get('/backstage/concerts');

        $response->assertStatus(302);
        $response->assertRedirect(route('auth.login'));
    }

    /** @test */
    public function promoters_can_only_view_a_list_of_their_own_concerts()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $concertA = Concert::factory()->create(['user_id' => $user->id]);
        $concertB = Concert::factory()->create(['user_id' => $user->id]);
        $concertC = Concert::factory()->create(['user_id' => $otherUser->id]);
        $concertD = Concert::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get('/backstage/concerts');

        $response->assertStatus(200);
        $response->data('concerts')->assertContains($concertA);
        $response->data('concerts')->assertContains($concertB);
        $response->data('concerts')->assertContains($concertD);

        $response->data('concerts')->assertNotContains($concertC);

        // $response->assertViewIs('concerts.index');
        // $response->assertSee($concert[0]->title);
    }
}
