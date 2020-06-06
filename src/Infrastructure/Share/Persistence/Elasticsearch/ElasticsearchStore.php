<?php

declare(strict_types=1);

namespace App\Infrastructure\Share\Persistence\Elasticsearch;

use Elasticsearch\Client;
use Elasticsearch\ClientBuilder;
use Psr\Log\LoggerInterface;

abstract class ElasticsearchStore
{
    private Client $client;

    public function __construct(array $elasticConfig, LoggerInterface $elasticsearchLogger = null)
    {
        $defaultConfig = [];

        if ($elasticsearchLogger) {
            $defaultConfig['logger'] = $elasticsearchLogger;
            $defaultConfig['tracer'] = $elasticsearchLogger;
        }

        $this->client = ClientBuilder::fromConfig(\array_replace($defaultConfig, $elasticConfig), true);
    }

    abstract protected function index(): string;

    public function refresh(): void
    {
        if ($this->client->indices()->exists(['index' => $this->index()])) {
            $this->client->indices()->refresh(['index' => $this->index()]);
        }
    }

    public function delete(): void
    {
        if ($this->client->indices()->exists(['index' => $this->index()])) {
            $this->client->indices()->delete(['index' => $this->index()]);
        }
    }

    public function reboot(): void
    {
        $this->delete();
        $this->boot();
    }

    public function boot(): void
    {
        if (!$this->client->indices()->exists(['index' => $this->index()])) {
            $this->client->indices()->create(['index' => $this->index()]);
        }
    }

    protected function add(array $document): array
    {
        $query = [];

        $query['index'] = $this->index();
        $query['id'] = $document['id'] ?? null;
        $query['body'] = $document;

        return $this->client->index($query);
    }
}
