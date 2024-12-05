<?php

namespace App\Http\Controllers;

use App\Clients\AlgoliaSearchClient;
use Illuminate\Http\Request;

class EmailSearchController extends Controller
{
    public function __invoke(string $term)
    {
        $algolia_index_name = config('scout.prefix') . '_new_profile_builder_templates';
        $results= AlgoliaSearchClient::getAlgoliaResultsByIndex($term, $algolia_index_name);
        return response()->json($results);
    }
}
