<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Elastic\Elasticsearch\ClientBuilder;
use App\Models\Location;
class locationIndexElasticSearch extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Elastic:createLocationIndex';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a index of location in Elastic Search';

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
        try{
            $startTime          = microtime(true); // Record the start time
            $startTimestamp     = (int) round($startTime); // set timestamp of start time
            $startFormattedTime = date("h:i:s", $startTimestamp); // set format of start time

            $this->info("------------ Command Execution Start ------------");
            $this->info("------------ Execute At : $startFormattedTime  ------------");
               
            Log::info('Start checking Index....');
            
            // Elasticsearch client configuration
            $client = ClientBuilder::create()->build();
            $ping = $client->info();
            dd($client);
            // Define the index name
            $indexName = 'location_index';

            // Create or update the Elasticsearch index
            $indexCreated = $this->createOrUpdateIndex($client, $indexName);

            Log::info('End checking Index....');
            // check the index and update the data
            if($indexCreated){            
                Log::info('Start Data update or import....');
            
                // Index or update data in Elasticsearch
                $result = $this->indexOrUpdateData($client, $indexName);

                Log::info('End Data update or import....');
            
                if($result){
                    Log::info('Data indexed successfully....');
                }
            }

            // Record the end time
            $endTime          = microtime(true);  // Record the end time
            $endTimestamp     = (int) round($endTime); // set timestamp of end time
            $endFormattedTime = date("h:i:s", $endTimestamp); // set format of end time

            // Calculate the execution time
            $executionTime          = $endTime - $startTime; // calculate the execution time
            $executionTimestamp     = (int) round($executionTime);  // set timestamp of execution time
            $executionFormattedTime = date("h:i:s", $executionTimestamp); // set format of execution time

            $this->info("------------ Execution End At : $endFormattedTime  ------------");
            $this->info("------------ Execution Time Consumed : $executionFormattedTime  ------------");
            $this->info("------------ Command Execution End ------------");
        
        } catch (\Exception $e) {
            // Log or handle the exception 
            Log::info('Exception : Error Occured....');
            Log::error($e->getMessage());
        }
    }

    // check if index exist or create new index function
    public function createOrUpdateIndex($client, $indexName)
    {
        try {
            // Check if the index exists
            $indexExists = $client->indices()->exists(['index' => $indexName]);
            
            // Create the index if it doesn't exist
            if (!$indexExists) {
                $this->createIndex($client,$indexName);
            }
        }catch (\NotFoundException $e) {
            // Handle 404 (index not found)
            Log::info("Index '$indexName' not found. Creating...\n");
            $this->createIndex($client,$indexName);        
        }

        // return true if index exists
        return true;
        
    }

    // Create index setting params function
    public function indexSetting($indexName){
        // Define the index settings
        $indexSettings = [
            'number_of_shards' => 2,
            'number_of_replicas' => 0,
        ];

        $params = [
            'index' => $indexName,
            'body'  => [
                'settings' => $indexSettings,
                'mappings' => [
                    'properties' => [
                        'location_id'   => ['type' => 'integer'],
                        'location_name' => ['type' => 'text'],
                        'location_code' => ['type' => 'text'],
                        'description'   => ['type' => 'text'],
                        'location_type' => ['type' => 'text'],
                        'warehouse_id'  => ['type' => 'integer']
                    ],
                ],
            ],
        ];
        // return params to create a new index
        return $params;
    }

    public function createIndex($client,$indexName){
        $params   = $this->indexSetting($indexName);

        $response = $client->indices()->create($params);

        if ($response['acknowledged']) {
            Log::info("Index '.$indexName.' created successfully....\n");            
            // return true on index created
            return true;
        } else {
            Log::info("Failed to create the index....\n");
            // return false on failed
            return false;
        }
    }

    public function indexOrUpdateData($client, $indexName)
    {
        // Start measuring memory usage
        $startMemoryUsage     = round(memory_get_usage() / (1024 * 1024), 2);
        $startPeakMemoryUsage = round(memory_get_peak_usage() / (1024 * 1024), 2);
        
        Log::info("Start Memory Usage: " . $startMemoryUsage . " MB....\n");
        Log::info("Start Peak Memory Usage: " . $startPeakMemoryUsage . " MB....\n");
        
        $startTime            = microtime(true); // Record the start time
        $chunkSize            = 5000; // Adjust the        chunk size based on your needs
        $maxRetries           = 3; // Maximum numbe  r of retries for a failed bulk request
        
        // Get Items from database     
        Location::where('deleted', '=', '0')->chunk($chunkSize, function ($locations) use ($client, $indexName, $maxRetries) {
            $params = ['body' => []];

            foreach ($locations as $location) {
                $params['body'][] = [
                    'index' => [
                        '_index' => $indexName,
                        '_id'    => $location->id,
                    ],
                ];
               
                $params['body'][] = [
                    'location_id'   => $location->id,
                    'location_name' => $location->name,
                    'location_code' => $location->code,
                    'description'   => $location->description,
                    'location_type' => $location->location_type,
                    'warehouse_id'  => $location->warehouse_id
                ];
            }

            // Use the Bulk API to send multiple index requests in a si  ngle request
            $retries = 0;
            $success = false;

            while (!$success && $retries < $maxRetries) {
                try {
                    $client->bulk($params);
                    $success = true;
                } catch (\Exception $e) {
                    // Log the error or handle it as needed
                    Log::error("Error indexing bulk request: " . $e->getMessage());
                    $retries++;
                    usleep(500000); // Wait for 0.5 seconds before retrying
                }
            }

            if (!$success) {
                Log::error("Failed to index bulk request after $maxRetries retries.");
            } else {
                Log::info("Data indexed or updated successfully....\n");
            }

            // Clear the params array for the next iteration
            $params = ['body' => []];
        });

        // End measuring memory usage
        $endMemoryUsage     = round(memory_get_usage() / (1024 * 1024), 2);
        $endPeakMemoryUsage = round(memory_get_peak_usage() / (1024 * 1024), 2);
        Log::info("End Memory Usage: " . $endMemoryUsage . " MB....\n");
        Log::info("End Peak Memory Usage: " . $endPeakMemoryUsage . " MB....\n");
        
        // Record the end time
        $endTime = microtime(true);

        // Calculate the execution time
        $executionTime = $endTime - $startTime;

        // Output the result
        Log::info("Script executed in $executionTime seconds\n");

        return true;
    }

}
