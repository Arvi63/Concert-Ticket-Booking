<?php
namespace Tests\Feature\Backstage;

use App\Jobs\SendAttendeeMessage;
use App\Models\AttendeeMessage;
use App\Models\Concert;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class MessageAttendeesTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function a_promoter_can_send_a_new_message()
    {
        $user = User::factory()->create();
        $concert = Concert::factory()->createPublished([
            'user_id' => $user->id,
        ]);

        Queue::fake();

        $response = $this->actingAs($user)->post("backstage/concerts/{$concert->id}/message", [
            'subject' => 'My subject',
            'message' => 'My message',
        ]);

        $response->assertRedirect(route('backstage.concert-message.create', $concert));
        $response->assertSessionHas('flash');

        $message = AttendeeMessage::first();
        $this->assertEquals($concert->id, $message->concert_id);
        $this->assertEquals('My subject', $message->subject);
        $this->assertEquals('My message', $message->message);

        Queue::assertPushed(SendAttendeeMessage::class, function ($job) use ($message) {
            return $job->attendeeMessage->is($message);
        });
    }
}
