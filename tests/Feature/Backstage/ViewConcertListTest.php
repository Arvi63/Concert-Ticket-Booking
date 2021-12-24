<?php
namespace Tests\Feature\Backstage;

use App\Models\Concert;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class ViewConcertListTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function guests_cannot_view_a_promoters_concert_list()
    {
        $this->withExceptionHandling();

        $response = $this->get('/backstage/concerts');

        $response->assertStatus(302);
        $response->assertRedirect(route('auth.login'));
    }

    /** @test */
    public function promoters_can_only_view_a_list_of_their_own_concerts()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $publishedConcertA = Concert::factory()->createPublished(['user_id' => $user->id]);
        $publishedConcertB = Concert::factory()->createPublished(['user_id' => $otherUser->id]);
        $publishedConcertC = Concert::factory()->createPublished(['user_id' => $user->id]);

        $unpublishedConcertA = Concert::factory()->createUnpublished(['user_id' => $user->id]);
        $unpublishedConcertB = Concert::factory()->createUnpublished(['user_id' => $otherUser->id]);
        $unpublishedConcertC = Concert::factory()->createUnpublished(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get('/backstage/concerts');

        $response->assertStatus(200);

        $response->data('publishedConcerts')->assertEquals([
            $publishedConcertA,
            $publishedConcertC,
        ]);

        $response->data('unpublishedConcerts')->assertEquals([
            $unpublishedConcertA,
            $unpublishedConcertC,
        ]);

        // $response->data('publishedConcerts')->assertContains($publishedConcertA);
        // $response->data('publishedConcerts')->assertNotContains($unpublishedConcertB);
        // $response->data('publishedConcerts')->assertContains($publishedConcertC);
        // $response->data('publishedConcerts')->assertNotContains($unpublishedConcertA);
        // $response->data('publishedConcerts')->assertNotContains($publishedConcertB);
        // $response->data('publishedConcerts')->assertNotContains($unpublishedConcertC);

        // $response->data('unpublishedConcerts')->assertContains($unpublishedConcertA);
        // $response->data('unpublishedConcerts')->assertNotContains($publishedConcertB);
        // $response->data('unpublishedConcerts')->assertContains($unpublishedConcertC);
        // $response->data('unpublishedConcerts')->assertNotContains($publishedConcertA);
        // $response->data('unpublishedConcerts')->assertNotContains($unpublishedConcertB);
        // $response->data('unpublishedConcerts')->assertNotContains($publishedConcertC);

        // $response->assertViewIs('concerts.index');
        // $response->assertSee($concert[0]->title);
    }
}
