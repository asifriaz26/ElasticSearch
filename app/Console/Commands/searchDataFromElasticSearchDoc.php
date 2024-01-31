<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Elastic\Elasticsearch\ClientBuilder;

class searchDataFromElasticSearchDoc extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Elastic:searchItem {itemId}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Search Data from Item Index document in Elastic Search';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $indexName   = 'items_index';
        $searchQuery = $this->argument('itemId');
        
        // Elasticsearch client configuration
        $client = ClientBuilder::create()->build();

        // Search data in Elasticsearch
        $this->searchData($client, $indexName, $searchQuery);

    }

    // Function to search data in Elasticsearch
    public function searchData($client, $indexName, $query)
    {
        $params = [
            'index' => $indexName,
            'body' => [
                'query' => [
                    'match_pharase' => [
                        'item_title_en' => $query, // Replace 'name' with the field you want to search
                    ],
                    // "match" => [
                    //     "item_title_en" => [
                    //       "query" => $query,
                    //       "operator" => "or",  // Use "and" to require all terms to match
                    //     ],
                    // ],
                ],
            ],
        ];

        $response = $client->search($params);

        // Process the search results
        foreach ($response['hits']['hits'] as $hit) {
            $source = $hit['_source'];
            // Do something with the search result data
            echo "Item ID: {$hit['_id']}, Item Title: {$source['item_title_en']}, Price: {$source['price']}\n";
        }
    }
}
