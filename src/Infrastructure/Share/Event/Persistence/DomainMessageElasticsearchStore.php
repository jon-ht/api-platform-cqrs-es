<?php

declare(strict_types=1);

namespace App\Infrastructure\Share\Event\Persistence;

use App\Infrastructure\Share\Persistence\Elasticsearch\ElasticsearchStore;
use Broadway\Domain\DomainMessage;
use Psr\Log\LoggerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class DomainMessageElasticsearchStore extends ElasticsearchStore
{
    private NormalizerInterface $normalizer;

    public function __construct(
        array $elasticConfig,
        NormalizerInterface $normalizer,
        LoggerInterface $elasticsearchLogger = null
    ) {
        parent::__construct($elasticConfig, $elasticsearchLogger);

        $this->normalizer = $normalizer;
    }

    protected function index(): string
    {
        return 'events';
    }

    public function store(DomainMessage $message): void
    {
        $document = [
            'type' => $message->getType(),
            'payload' => $this->normalizer->normalize($message->getPayload()),
            'occurred_on' => $message->getRecordedOn()->toString(),
        ];

        $this->add($document);
    }
}
