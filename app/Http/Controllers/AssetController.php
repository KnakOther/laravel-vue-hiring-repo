<?php

namespace App\Http\Controllers;

use App\Clients\KnakAPIClient;
use App\Models\Recipient;
use App\Services\EmailRenderService;
use Illuminate\Support\Facades\Log;

class AssetController extends Controller
{
    public function show(string $id)
    {
        $knak_api_client = new KnakAPIClient();
        $asset = $knak_api_client->getAsset($id);
        $recipient_id = request()->get('recipient_id');
        if($recipient_id === null) {
            $recipient_id = Recipient::first()->id;
        }
        $email_render_service = new EmailRenderService();
        $asset['html'] = $email_render_service($recipient_id, $asset['html']);
        return response()->json($asset);
    }
}
