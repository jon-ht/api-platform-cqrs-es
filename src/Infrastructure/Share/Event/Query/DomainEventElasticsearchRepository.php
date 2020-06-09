<?php

declare(strict_types=1);

namespace App\Infrastructure\Share\Event\Query;

use App\Domain\Shared\DomainEvent;
use App\Infrastructure\Share\Query\Repository\ElasticsearchRepository;
use Broadway\Domain\DomainMessage;
use Psr\Log\LoggerInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class DomainEventElasticsearchRepository extends ElasticsearchRepository
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
        $domainEvent = new DomainEvent(
            Uuid::uuid4()->toString(),
            $message->getType(),
            $message->getPayload(),
            $message->getRecordedOn()->toString()
        );

        $document = (array) $this->normalizer->normalize($domainEvent);

        $this->add($document);
    }
}
