<?php

namespace App\Services;

use App\Models\Campaign;
use App\Models\CampaignRecipient;
use Illuminate\Support\Facades\Log;

class RegisterEmailEventsService
{
    public static function registerEmailEvents(string $message_id, string $event,string $event_time, string $failure_reason = null)
    {
        try {
            CampaignRecipient::where('message_id', $message_id)->update([
                $event . '_at' => $event_time,
                'failure_reason' => $failure_reason
            ]);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }
    }
}
