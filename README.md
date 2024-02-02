# Laravel Elasticsearch Integration

A Laravel project demonstrating Elasticsearch integration for efficient data indexing and searching.

## Prerequisites

Before getting started, ensure you have the following installed:

- [PHP](https://www.php.net/) (>= 7.4)
- [Composer](https://getcomposer.org/)
- [Elasticsearch](https://www.elastic.co/elasticsearch/)

## Installation

1. Clone the repository:

   ```bash
   git clone https://github.com/asifriaz26/ElasticSearch.git

2. Update Composer

    ```update composer
    update all dependencies to their latest versions based on the version constraints in the `composer.json`

## Configuration

- Update the `.env` file with the Elasticsearch server details:

  ```env
  ELASTICSEARCH_HOST=http://localhost
  ELASTICSEARCH_PORT=9200
  ELASTICSEARCH_SCHEME=http
  ELASTICSEARCH_USER=
  ELASTICSEARCH_PASS=

- Update the database file with Elasticsearch server detail

  ```database
  'elasticsearch' => [
      'driver' => 'elasticsearch',
      'hosts' => [
          [
              'host' => env('ELASTICSEARCH_HOST', '127.0.0.1'),
              'port' => env('ELASTICSEARCH_PORT', 9200),
              'scheme' => env('ELASTICSEARCH_SCHEME', 'http'),
              'user' => env('ELASTICSEARCH_USER'),
              'pass' => env('ELASTICSEARCH_PASS'),
          ],
      ],
  ],

- Config cache

    -after configuring .env file config cache by running artisan command
    
    ```artisan command
    
    -php artisan config:cache



## License

This project is licensed under the <a href="https://opensource.org/license/mit/" target="_blank">MIT License </a>.

## Acknowledgments

- [Laravel](https://laravel.com/)
- [Elasticsearch](https://www.elastic.co/elasticsearch/)


