<?php

namespace App\Http\Controllers;

use App\Services\EmailEvent;
use App\Services\RegisterEmailEventsService;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SnsNotificationController extends Controller
{
    public function __invoke(Request $request)
    {
        if (!$this->isValidSnsSubscription($request)) {
            return response()->json(['error' => 'Invalid SNS subscription'], 403);
        }
        $notification = json_decode($request->getContent(), true);
        $message = json_decode($notification['Message'], true);
        $notification_type = $message['eventType'] ?? null;
        $mail = $message['mail'] ?? [];
        $message_id = $mail['messageId'] ?? null;
        $failure_reason = null;
        switch ($notification_type) {
            case 'Bounce':
                $event_time = $message['bounce']['timestamp'] ?? [];
                $event = EmailEvent::BOUNCED;
                break;
            case 'Delivery':
                $event_time = $message['delivery']['timestamp'] ?? [];
                $event = EmailEvent::DELIVERED;
                break;
            case 'Open':
                $event_time = $message['open']['timestamp'] ?? [];
                $event = EmailEvent::OPENED;
                break;
            case 'DeliveryDelay':
                $event_time = $message['deliveryDelay']['timestamp'] ?? [];
                $event = EmailEvent::FAILED;
                $failure_reason = $message['deliveryDelay']['delayType'] ?? null;
                break;
            case 'Click':
                $event_time = $message['click']['timestamp'] ?? [];
                $event = EmailEvent::CLICKED;
                break;
            default:
                Log::info('Unknown event type');
                return;
        }
        $date_time = new DateTime($event_time);

        $laravel_date = $date_time->format('Y-m-d H:i:s');
        RegisterEmailEventsService::registerEmailEvents($message_id, $event, $laravel_date, $failure_reason);
        // Return a successful response
        return response()->json(['message' => 'Notification processed successfully'], 200);
    }

    protected function isValidSnsSubscription(Request $request)
    {
        $expected_topic_arn = config('services.sns.topic_arn');
        $topic_arn = $request->header('x-amz-sns-topic-arn');
        return $topic_arn === $expected_topic_arn;
    }
}
