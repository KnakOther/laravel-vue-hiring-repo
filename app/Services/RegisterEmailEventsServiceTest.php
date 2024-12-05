<?php

namespace App\Services;

use App\Models\Campaign;
use App\Models\Recipient;
use DateTime;
use Tests\TestCase;
class RegisterEmailEventsServiceTest extends TestCase
{
    /** @test */
    public function testRegisterEmailEvents()
    {

        $awsTimestamp = '2022-04-17T12:34:56Z'; // Example AWS timestamp

        $dateTime = new DateTime($awsTimestamp);
        $laravelDate = $dateTime->format('Y-m-d H:i:s');


        $message_id = '1234';
        $event = EmailEvent::FAILED;
        $campaign = Campaign::factory()->create();
        $recipient = Recipient::factory()->create();
        $campaign->recipients()->attach($recipient->id, ['message_id' => $message_id,'html' => $campaign->html]);

        RegisterEmailEventsService::registerEmailEvents($message_id, $event,$laravelDate);
    }
}
