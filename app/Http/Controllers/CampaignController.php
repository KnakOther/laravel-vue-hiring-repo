<?php

namespace App\Http\Controllers;

use App\Clients\KnakAPIClient;
use App\Clients\KnakSESClient;
use App\Models\Campaign;
use App\Models\CampaignRecipient;
use App\Models\Recipient;
use App\Services\EmailRenderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CampaignController extends Controller
{

    public function index()
    {
        $campaigns = Campaign::all();

        // return as json
        return response()->json($campaigns);
    }

    public function store(Request $request)
    {
        $knak_api_client = new KnakAPIClient();
        $asset = $knak_api_client->getAsset($request->get('asset_id'));

        if ($request->get('scheduled')) {
            Log::info('Scheduling campaign');
            $campaign = Campaign::create([
                'send_date' => $request->get('send_date'),
                'email_name' => $asset['name'],
                'subject' => $asset['subject'],
                'from_name' => $asset['from_name'],
                'from_email' => $asset['from_email'],
                'reply_email' => $asset['reply_email'],
                'html' => $asset['html'],
                'knak_email_id' => $asset['id'],
                'knak_version' => $asset['version'],
                'scheduled' => true,
            ]);
        } else {
            $campaign = Campaign::create([
                'send_date' => now(),
                'email_name' => $asset['name'],
                'subject' => $asset['subject'],
                'from_name' => $asset['from_name'],
                'from_email' => $asset['from_email'],
                'reply_email' => $asset['reply_email'],
                'html' => $asset['html'],
                'knak_email_id' => $asset['id'],
                'knak_version' => $asset['version'],
            ]);
        }

        $recipients = $this->resolveRecipients($request->get('filterType'), $request->get('filterValue'));
        Log::info($recipients);

        foreach ($recipients as $recipient) {
            $email_render_service = new EmailRenderService();
            $asset['html'] = $email_render_service($recipient->id, $asset['html']);

            $campaign->recipients()->attach($recipient->id, [
                'html' => $asset['html']
            ]);

            if ($campaign->scheduled) {
                continue;
            }

            $ses_client = new KnakSESClient();
            $results = $ses_client->send(
                reply: $asset['reply_email'],
                from: $asset['from_email'],
                to: [$recipient->email],
                subject: $asset['subject'],
                html: $asset['html'],
                text: $asset['text']);

            if (isset($results[0]['error'])) {
                // add failure to campaign recipient
                $campaign->recipients()->updateExistingPivot($recipient->id, [
                    'failed_at' => date('Y-m-d H:i:s'),
                    'failure_reason' => 'Error sending to email address'
                ]);
            } else {
                //update campaign_recipient with messageId
                $campaign->recipients()->updateExistingPivot($recipient->id, [
                    'message_id' => $results[0]['MessageId'],
                    'sent_at' => date('Y-m-d H:i:s')
                ]);
            }
        }

        return response()->json($campaign);
    }

    protected function resolveRecipients($filter, $value)
    {
        if ($filter === 'user') {
            return Recipient::whereIn('id', $value)->get();
        }

        return Recipient::whereIn($filter, $value)->get();
    }

    public function show($id)
    {
        $campaign = Campaign::find($id);

        return response()->json($campaign);
    }

    public function update(Request $request, $id)
    {
        $campaign = Campaign::find($id);
        $campaign->update($request->all());

        return response()->json($campaign);
    }

    public function destroy($id)
    {
        $campaign = Campaign::find($id);
        $campaign->delete();

        return response()->json(['message' => 'Campaign deleted']);
    }

    public function sendScheduledEmails()
    {
        $campaigns = Campaign::where('send_date', '<=', now())->where('scheduled', true)->get();

        foreach ($campaigns as $campaign) {
            $recipients = $campaign->recipients;

            foreach ($recipients as $recipient) {
                $ses_client = new KnakSESClient();
                $results = $ses_client->send(
                    reply: $campaign->reply_email,
                    from: $campaign->from_email,
                    to: [$recipient->email],
                    subject: $campaign->subject,
                    html: CampaignRecipient::where('campaign_id', $campaign->id)->where('recipient_id', $recipient->id)->first()->html,
                    text: ''
                );

                if (isset($results[0]['error'])) {
                    // add failure to campaign recipient
                    $campaign->recipients()->updateExistingPivot($recipient->id, [
                        'failed_at' => date('Y-m-d H:i:s'),
                        'failure_reason' => 'Error sending to email address'
                    ]);
                } else {
                    //update campaign_recipient with messageId
                    $campaign->recipients()->updateExistingPivot($recipient->id, [
                        'message_id' => $results[0]['MessageId'],
                        'sent_at' => date('Y-m-d H:i:s')
                    ]);
                }
            }

            $campaign->update(['scheduled' => false]);
        }

        return response()->json(['message' => 'Scheduled emails sent']);
    }
}
