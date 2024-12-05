<?php

namespace App\Clients;

use App\Clients\KnakAPIClient;
use Tests\TestCase;

class KnakSESClientTest extends TestCase
{
    /** @test */
    public function test_send_email()
    {
        $client = new KnakAPIClient();
        $asset_id = '63d414a8d3633';
        $asset = $client->getAsset($asset_id);

        $ses_client = new KnakSESClient();
        $result = $ses_client->send(reply:'info@emailsending.knak.link' , from:'info@emailsending.knak.link', to:['patrick@knak.com','fernando@knak.com'], subject:'Knak Send - Test', html:$asset['html'], text:$asset['text']);

    }
}
