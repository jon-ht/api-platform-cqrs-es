<?php

declare(strict_types=1);

namespace App\Application\Query\Event\GetEvents;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use App\Application\Query\CollectionQueryFactory;

class GetEventsQueryDataTransformer implements DataTransformerInterface
{
    private CollectionQueryFactory $factory;

    public function __construct(CollectionQueryFactory $factory)
    {
        $this->factory = $factory;
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        return GetEventsQuery::class === ($context['query'] ?? false);
    }

    public function transform($object, string $to, array $context = [])
    {
        return $this->factory->createCollectionQuery(GetEventsQuery::class, $context);
    }
}
