<?php
namespace Database\Factories;

use App\Models\Concert;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class ConcertFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => function () {
                return User::factory()->create()->id;
            },
            'title' => 'Example Band',
            'subtitle' => 'with the Fake Openers',
            'additional_information' => 'Some sample additional information',
            'date' => Carbon::parse('+2 weeks'),
            'venue' => 'The Example Theatre',
            'venue_address' => '123 Example Lane',
            'city' => 'Fakeville',
            'state' => 'ON',
            'zip' => '90989',
            'ticket_price' => 2000,
            'ticket_quantity' => 5,
        ];
    }

    public function published()
    {
        return $this->state([
            'published_at' => Carbon::parse('-1 week'),
        ]);
    }

    public function unpublished()
    {
        return $this->state([
            'published_at' => null,
        ]);
    }

    public static function createPublished($overrides = [])
    {
        $concert = Concert::factory()->create($overrides);

        $concert->publish();

        return $concert;
    }

    public static function createUnpublished($overrides = [])
    {
        return Concert::factory()->create($overrides);
    }
}
