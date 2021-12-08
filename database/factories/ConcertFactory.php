<?php

namespace Database\Factories;

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
            'date' => Carbon::parse('+2 weeks'),
            'ticket_price' => 2000,
            'venue' => 'The Example Theatre',
            'venue_address' => '123 Example Lane',
            'city' => 'Fakeville',
            'state' => 'ON',
            'zip' => '90989',
            'additional_information' => 'Some sample additional information',
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
}
