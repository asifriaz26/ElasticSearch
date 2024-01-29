<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Elastic\Elasticsearch\ClientBuilder;

class deleteIndexElasticSearch extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Elastic:deleteIndex';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete all indexes from Elastic Search';

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
        // Create Elasticsearch client
        $client     = ClientBuilder::create()->build();

        // Get a list of all indices
        $response   = $client->cat()->indices(['format' => 'json']);

        // Extract index names from the response
        $data       = json_decode($response->getBody(), true);

        // Fetch specific columns (fields)
        $indexNames = array_column($data, 'index');
    
        // Delete each index
        foreach ($indexNames as $index) {
            $client->indices()->delete(['index' => $index]);
            echo "Index '$index' deleted.\n";
        }
        echo "All indices deleted.\n";
    }
}
