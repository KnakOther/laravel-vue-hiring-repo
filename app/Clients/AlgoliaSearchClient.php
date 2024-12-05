<?php

namespace App\Clients;

use Algolia\AlgoliaSearch\SearchClient;
use Illuminate\Support\Facades\Log;

class AlgoliaSearchClient
{
    public static function getAlgoliaResultsByIndex($term, $index_name)
    {
        $algolia_client = SearchClient::create(
            config('scout.algolia.id'),
            config('scout.algolia.secret'),
        );
        $index = $algolia_client->initIndex($index_name);
        $results = $index->search($term, [
            'hitsPerPage' => 10,
            'filters' => 'type:email AND company_id:6112d11b8e909',
        ]);
        return $results;
    }
}
