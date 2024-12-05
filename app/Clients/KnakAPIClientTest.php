<?php

use App\Clients\KnakAPIClient;
use Tests\TestCase;

class KnakAPIClientTest extends TestCase
{
    /** @test */
    public function test_get_asset()
    {
        $client = new KnakAPIClient();
        $asset_id = '650dc659416b3';
        $response = $client->getAsset($asset_id);
        $this->assertIsString($response);
    }
}
