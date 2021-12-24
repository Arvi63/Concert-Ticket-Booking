<?php
namespace Tests\Feature\Backstage;

use App\Models\Concert;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Testing\Assert as PHPUnit;
use Tests\TestCase;

class EditConcertTest extends TestCase
{
    use DatabaseMigrations;
    // edit form test left to be written

    private function oldAttributes($overrides = [])
    {
        return array_merge([
            'title' => 'Old title',
            'subtitle' => 'Old Subtitle',
            'date' => Carbon::parse('2017-01-01 5:00pm'),
            'ticket_price' => 2000,
            'venue' => 'Old Venue',
            'venue_address' => 'Old address',
            'city' => 'Old city',
            'state' => 'Old state',
            'zip' => '0000',
            'additional_information' => 'Old additional information',
            'ticket_quantity' => 5,
        ], $overrides);
    }

    private function validParams($overrides = [])
    {
        return array_merge([
            'title' => 'New title',
            'subtitle' => 'New Subtitle',
            'additional_information' => 'New additional information',
            'date' => '2022-12-12',
            'time' => '8:00pm',
            'venue' => 'New Venue',
            'venue_address' => 'New address',
            'city' => 'New city',
            'state' => 'New state',
            'zip' => '9999',
            'ticket_price' => '72.50',
            'ticket_quantity' => 3,
        ], $overrides);
    }

    /** @test */
    public function promoters_can_update_their_own_unpublished_concerts()
    {
        $user = User::factory()->create();
        $concert = Concert::factory()->create([
            'user_id' => $user->id,
            'title' => 'Old title',
            'subtitle' => 'Old Subtitle',
            'date' => Carbon::parse('2017-01-01 5:00pm'),
            'ticket_price' => 2000,
            'venue' => 'Old Venue',
            'venue_address' => 'Old address',
            'city' => 'Old city',
            'state' => 'Old state',
            'zip' => '0000',
            'additional_information' => 'Old additional information',
            'ticket_quantity' => 5,
        ]);

        $this->assertFalse($concert->isPublished());

        $response = $this->actingAs($user)->patch("/backstage/concerts/{$concert->id}", [
            'title' => 'New title',
            'subtitle' => 'New Subtitle',
            'date' => '2022-12-12',
            'time' => '8:00pm',
            'ticket_price' => '72.50',
            'venue' => 'New Venue',
            'venue_address' => 'New address',
            'city' => 'New city',
            'state' => 'New state',
            'zip' => '9999',
            'additional_information' => 'New additional information',
            'ticket_quantity' => 10,
        ]);

        $response->assertRedirect('/backstage/concerts');

        tap($concert->fresh(), function ($concert) {
            $this->assertEquals('New title', $concert->title);
            $this->assertEquals('New Subtitle', $concert->subtitle);
            $this->assertEquals('New additional information', $concert->additional_information);
            $this->assertEquals(Carbon::parse('2022-12-12 8:00pm'), $concert->date);
            $this->assertEquals('New Venue', $concert->venue);
            $this->assertEquals('New address', $concert->venue_address);
            $this->assertEquals('New city', $concert->city);
            $this->assertEquals('New state', $concert->state);
            $this->assertEquals('9999', $concert->zip);
            $this->assertEquals(7250, $concert->ticket_price);
            $this->assertEquals(10, $concert->ticket_quantity);
        });
    }

    /** @test */
    public function promoters_cannot_update_other_unpublished_concerts()
    {
        $this->withExceptionHandling();

        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $concert = Concert::factory()->create($this->oldAttributes([
            'user_id' => $user->id,

        ]));

        $this->assertFalse($concert->isPublished());

        $response = $this->actingAs($otherUser)->patch("/backstage/concerts/{$concert->id}", $this->validParams());

        $response->assertStatus(404);

        PHPUnit::assertArraySubset($this->oldAttributes([
            'user_id' => $user->id,
        ]), $concert->fresh()->getAttributes());
    }

    /** @test */
    public function promoters_cannot_update_unpublished_concerts()
    {
        $this->withExceptionHandling();

        $user = User::factory()->create();
        $concert = Concert::factory()->published()->create([
            'user_id' => $user->id,
            'title' => 'Old title',
            'subtitle' => 'Old Subtitle',
            'additional_information' => 'Old additional information',
            'date' => Carbon::parse('2017-01-01 5:00pm'),
            'venue' => 'Old Venue',
            'venue_address' => 'Old address',
            'city' => 'Old city',
            'state' => 'Old state',
            'zip' => '0000',
            'ticket_price' => 2000,
            'ticket_quantity' => 5,

        ]);

        $this->assertTrue($concert->isPublished());

        $response = $this->actingAs($user)->patch("/backstage/concerts/{$concert->id}", [
            'title' => 'New title',
            'subtitle' => 'New Subtitle',
            'additional_information' => 'New additional information',
            'date' => '2022-12-12',
            'time' => '8:00pm',
            'venue' => 'New Venue',
            'venue_address' => 'New address',
            'city' => 'New city',
            'state' => 'New state',
            'zip' => '9999',
            'ticket_price' => '72.50',
            'ticket_quantity' => 10,
        ]);

        $response->assertStatus(403);

        tap($concert->fresh(), function ($concert) {
            $this->assertEquals('Old title', $concert->title);
            $this->assertEquals('Old Subtitle', $concert->subtitle);
            $this->assertEquals('Old additional information', $concert->additional_information);
            $this->assertEquals(Carbon::parse('2017-01-01 5:00pm'), $concert->date);
            $this->assertEquals('Old Venue', $concert->venue);
            $this->assertEquals('Old address', $concert->venue_address);
            $this->assertEquals('Old city', $concert->city);
            $this->assertEquals('Old state', $concert->state);
            $this->assertEquals('0000', $concert->zip);
            $this->assertEquals(2000, $concert->ticket_price);
            $this->assertEquals(5, $concert->ticket_quantity);
        });
    }

    /** @test */
    public function guests_cannot_update_concerts()
    {
        $this->withExceptionHandling();

        $user = User::factory()->create();
        $concert = Concert::factory()->create([
            'user_id' => $user->id,
            'title' => 'Old title',
            'subtitle' => 'Old Subtitle',
            'date' => Carbon::parse('2017-01-01 5:00pm'),
            'ticket_price' => 2000,
            'venue' => 'Old Venue',
            'venue_address' => 'Old address',
            'city' => 'Old city',
            'state' => 'Old state',
            'zip' => '0000',
            'additional_information' => 'Old additional information',
        ]);

        $this->assertFalse($concert->isPublished());

        $response = $this->patch("/backstage/concerts/{$concert->id}", [
            'title' => 'New title',
            'subtitle' => 'New Subtitle',
            'date' => '2022-12-12',
            'time' => '8:00pm',
            'ticket_price' => '72.50',
            'venue' => 'New Venue',
            'venue_address' => 'New address',
            'city' => 'New city',
            'state' => 'New state',
            'zip' => '9999',
            'additional_information' => 'New additional information',
        ]);

        $response->assertRedirect('/login');

        tap($concert->fresh(), function ($concert) {
            $this->assertEquals('Old title', $concert->title);
            $this->assertEquals('Old Subtitle', $concert->subtitle);
            $this->assertEquals('Old additional information', $concert->additional_information);
            $this->assertEquals(Carbon::parse('2017-01-01 5:00pm'), $concert->date);
            $this->assertEquals('Old Venue', $concert->venue);
            $this->assertEquals('Old address', $concert->venue_address);
            $this->assertEquals('Old city', $concert->city);
            $this->assertEquals('Old state', $concert->state);
            $this->assertEquals('0000', $concert->zip);
            $this->assertEquals(2000, $concert->ticket_price);
        });
    }
}
