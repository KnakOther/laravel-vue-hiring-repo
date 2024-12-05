<?php

namespace App\Clients;
use Aws\Ses\SesClient;
use Illuminate\Support\Facades\Log;

class KnakSESClient
{
    private SESClient $client;
    public function __construct()
    {
        $this->client = new SesClient([
            'version' => 'latest',
            'region' => config('services.ses.region'),
            'credentials' => [
                'key' => config('services.ses.key'),
                'secret' => config('services.ses.secret'),
            ],
        ]);

    }
    public function send( string $reply, string $from, array $to, string $subject, string $html, string $text)
    {
        $reply = 'patrick@knak.com';
        $from = 'info@emailsending.knak.link';
        $results = [];
        foreach ($to as $destination) {
            try {
                $result = $this->client->sendEmail([
                    'Source' => $from,
                    'ReplyToAddresses' => [$reply],
                    'Destination' => [
                        'ToAddresses' => [$destination],
                    ],
                    'Message' => [
                        'Subject' => [
                            'Charset' => 'UTF-8',
                            'Data' => $subject,
                        ],
                        'Body' => [
                            'Html' => [
                                'Charset' => 'UTF-8',
                                'Data' => $html,
                            ],
                            'Text' => [
                                'Charset' => 'UTF-8',
                                'Data' => $html,
                            ],
                        ],
                    ],
                ]);
            } catch (\Exception $e) {
                Log::error($e->getMessage());
                $result = ['error' => $e->getMessage()];
            }

            $results[] = $result;
        }
        return $results;
    }
}
