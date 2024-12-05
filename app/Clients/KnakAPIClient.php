<?php

namespace App\Clients;

use Illuminate\Support\Facades\Http;

class KnakAPIClient
{
    private $headers = [];
    public function __construct()
    {
        $this->headers = [
            'Authorization' => 'Bearer ' . config('knak.knak_api_key'),
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];
        return 'Knak API response';
    }

    public function getAsset(string $asset_id): array
    {
        $url = config('knak.knak_api_url') . '/assets/' . $asset_id . '/content?platform=gamma';
        $response = Http::withHeaders($this->headers)->get($url);
        if ($response->successful()) {
            return $response->json()['data']['attributes'];
        } else {
            throw new \Exception('Knak API request failed. URL: ' . $url . ' API Key: ' . config('knak.knak_api_key'));
        }
    }
}
