<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use Illuminate\Http\Request;

class CampaignRecipientController extends Controller
{
    public function index(Request $request, $campaignId)
    {
        $campaign = Campaign::find($campaignId);
        $recipients = $campaign->recipients;

        return response()->json($recipients);
    }

    public function delivered(Request $request, $campaignId, $recipientId)
    {
        $campaign = Campaign::find($campaignId);
        $campaign->recipients()->updateExistingPivot($recipientId, [
            'delivered_at' => date('Y-m-d H:i:s')
        ]);

        return response()->json(['message' => 'Recipient delivered']);
    }

    public function opened(Request $request, $campaignId, $recipientId)
    {
        $campaign = Campaign::find($campaignId);
        $campaign->recipients()->updateExistingPivot($recipientId, [
            'opened_at' => date('Y-m-d H:i:s')
        ]);

        return response()->json(['message' => 'Recipient opened']);
    }

    public function clicked(Request $request, $campaignId, $recipientId)
    {
        $campaign = Campaign::find($campaignId);
        $campaign->recipients()->updateExistingPivot($recipientId, [
            'clicked_at' => date('Y-m-d H:i:s'),
        ]);

        return response()->json(['message' => 'Recipient clicked']);
    }

    //bounced


    //failed
    public function failed(Request $request, $campaignId, $recipientId)
    {
        $campaign = Campaign::find($campaignId);
        $campaign->recipients()->updateExistingPivot($recipientId, [
            'failed_at' => date('Y-m-d H:i:s'),
            'failure_reason' => $request->failure_reason
        ]);

        return response()->json(['message' => 'Recipient failed']);
    }
}
