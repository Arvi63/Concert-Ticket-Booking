<?php
namespace Tests\Unit\Jobs;

use App\Jobs\SendAttendeeMessage;
use App\Mail\AttendeeMessageEmail;
use App\Models\AttendeeMessage;
use App\Models\Concert;
use App\Models\Order;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class SendAttendeeMessageTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function it_sends_the_message_to_all_concert_attendees()
    {
        Mail::fake();

        $concert = Concert::factory()->createPublished();
        $otherConcert = Concert::factory()->createPublished();
        $message = AttendeeMessage::create([
            'concert_id' => $concert->id,
            'subject' => 'My subject',
            'message' => 'My message',
        ]);
        $orderA = Order::factory()->createForConcert($concert, ['email' => 'ram@example.com']);
        $otherOrder = Order::factory()->createForConcert($otherConcert, ['email' => 'otherperson@example.com']);
        $orderB = Order::factory()->createForConcert($concert, ['email' => 'shyam@example.com']);
        $orderC = Order::factory()->createForConcert($concert, ['email' => 'mohan@example.com']);

        SendAttendeeMessage::dispatch($message);

        Mail::assertQueued(AttendeeMessageEmail::class, function ($mail) use ($message) {
            return $mail->hasTo('ram@example.com')
                && $mail->attendeeMessage->is($message);
        });

        Mail::assertQueued(AttendeeMessageEmail::class, function ($mail) use ($message) {
            return $mail->hasTo('shyam@example.com')
                && $mail->attendeeMessage->is($message);
            ;
        });

        Mail::assertQueued(AttendeeMessageEmail::class, function ($mail) use ($message) {
            return $mail->hasTo('mohan@example.com')
                && $mail->attendeeMessage->is($message);
            ;
        });

        Mail::assertNotQueued(AttendeeMessageEmail::class, function ($mail) {
            return $mail->hasTo('otherperson@example.com');
        });
    }

    /** @test */
    public function email_has_the_correct_subject_and_message()
    {
        $message = new AttendeeMessage([
            'subject' => 'My Subject',
            'message' => 'My message',
        ]);

        $email = new AttendeeMessageEmail($message);

        $this->assertEquals('My Subject', $email->build()->subject);
        $this->assertEquals('My message', trim($this->render($email)));
    }

    private function render($mailable)
    {
        $mailable->build();
        return view($mailable->textView, $mailable->buildViewData())->render();
    }
}
