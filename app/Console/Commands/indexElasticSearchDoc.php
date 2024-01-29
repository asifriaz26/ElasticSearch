<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Elastic\Elasticsearch\ClientBuilder;

use App\Models\Product;

class indexElasticSearchDoc extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Elastic:createIndexForItems';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create Index of Items in Elastic Search';
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

            // Define the index name
            $indexName = 'items_index';

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
                        'item_id'                    => ['type' => 'integer'],
                        'item_sku'                   => ['type' => 'text'],
                        'item_title_en'              => ['type' => 'text'],
                        'item_title_ar'              => ['type' => 'text'],
                        'user_id'                    => ['type' => 'integer'],
                        'seller_id'                  => ['type' => 'integer'],
                        'countryid'                  => ['type' => 'integer'],
                        'category'                   => ['type' => 'integer'],
                        'sub_category'               => ['type' => 'integer'],
                        'brand'                      => ['type' => 'integer'],
                        'family_number'              => ['type' => 'integer'],
                        'variation_id'               => ['type' => 'integer'],
                        'grade'                      => ['type' => 'integer'],
                        'validated'                  => ['type' => 'integer'],
                        'b2b_enabled'                => ['type' => 'boolean'],
                        'b2c_enabled'                => ['type' => 'boolean'],
                        'is_fbc'                     => ['type' => 'boolean'],
                        'is_fbs'                     => ['type' => 'boolean'],
                        'asin'                       => ['type' => 'text'],
                        'noon_id'                    => ['type' => 'text'],
                        'price'                      => ['type' => 'float'],
                        'quantity'                   => ['type' => 'float'],
                        'atp_enable'                 => ['type' => 'boolean'],
                        'ship_to_uae'                => ['type' => 'boolean'],
                        'ship_to_ksa'                => ['type' => 'boolean'],
                        'weight_class_id'            => ['type' => 'integer'],
                        'source'                     => ['type' => 'text'],
                        'market_price'               => ['type' => 'float'],
                        'b2b_price'                  => ['type' => 'float'],
                        'atp_quantity'               => ['type' => 'integer'],
                        'atp_available_quantity'     => ['type' => 'integer'],
                        'b2b_minimum_order_quantity' => ['type' => 'integer'],
                        'has_warranty'               => ['type' => 'boolean'],
                        'moved_to_flow'              => ['type' => 'boolean'],
                        'source_id'                  => ['type' => 'integer'],
                        'deleted'                    => ['type' => 'boolean'],
                        'status'                     => ['type' => 'text'],
                        'autocomplete'               => ['type' => 'search_as_you_type'],
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
        $chunkSize            = 10000; // Adjust the chunk size based on your needs
        $maxRetries           = 3; // Maximum number of retries for a failed bulk request

        // Get Items from database
        Product::where('status', '!=', 'rejected')->chunk($chunkSize, function ($items) use ($client, $indexName, $maxRetries) {
            $params = ['body' => []];

            foreach ($items as $item) {
                $params['body'][] = [
                    'index' => [
                        '_index' => $indexName,
                        '_id'    => $item->id,
                    ],
                ];
               
                $params['body'][] = [
                    'item_id'                    => $item->id,
                    'item_sku'                   => $item->skuid,
                    'item_title_en'              => $item->item_title,
                    'item_title_ar'              => $item->item_title_ar,
                    'user_id'                    => $item->user_id,
                    'seller_id'                  => $item->shop_id,
                    'countryid'                  => $item->countryid,
                    'category'                   => $item->category_id,
                    'sub_category'               => $item->sub_catid,
                    'brand'                      => $item->brand_id,
                    'family_number'              => $item->family_number,
                    'variation_id'               => $item->variation_code,
                    'grade'                      => $item->grade,
                    'validated'                  => $item->validated,
                    'b2b_enabled'                => $item->b2b_enabled,
                    'b2c_enabled'                => $item->b2c_enabled,
                    'atp_enable'                 => $item->atp_enable,
                    'ship_to_uae'                => $item->ship_to_uae,
                    'ship_to_ksa'                => $item->ship_to_ksa,
                    'weight_class_id'            => $item->weight_class_id,
                    'source'                     => $item->source,
                    'is_fbc'                     => $item->is_fbc,
                    'is_fbs'                     => $item->is_fbs,
                    'asin'                       => $item->asin,
                    'noon_id'                    => $item->noon_id,
                    'price'                      => $item->price,
                    'market_price'               => $item->market_price,
                    'b2b_price'                  => $item->b2b_price,
                    'quantity'                   => $item->quantity,
                    'atp_quantity'               => $item->atp_quantity,
                    'atp_available_quantity'     => $item->atp_available_quantity,
                    'b2b_minimum_order_quantity' => $item->b2b_minimum_order_quantity,
                    'has_warranty'               => $item->has_warranty,
                    'moved_to_flow'              => $item->moved_to_flow,
                    'source_id'                  => $item->source_id,
                    'deleted'                    => $item->deleted,
                    'status'                     => $item->status
                ];
            }

            // Use the Bulk API to send multiple index requests in a single request
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
