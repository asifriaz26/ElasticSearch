<?php

namespace App\Http\Controllers;
use Elastic\Elasticsearch\ClientBuilder;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;

class ElasticSearchController extends Controller
{
    protected $elasticsearchClient;
    private   $itemIndexFile = 'items_index';

    public function __construct()
    {
        // Initialize the Elasticsearch client in the constructor
        $this->elasticsearchClient = ClientBuilder::create()->build();
    }


    public function searchItemFromElastic(Request $request){
        $searchText = $request->get('q');
        
        $response = $this->searchData($this->elasticsearchClient, $this->itemIndexFile, $searchText);
        
        return $response;
    }

    public function searchData($client, $indexName, $query)
    {
        $params = [
            'index' => $indexName,
            'body' => [
                'query' => [
                    'match' => [
                        'item_title_en' => $query, // Replace 'name' with the field you want to search
                    ],
                ],
            ],
        ];

        $response = $client->search($params);
        
        return $response['hits']['hits'];
    }

}
