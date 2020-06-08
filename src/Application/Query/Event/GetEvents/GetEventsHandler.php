<?php

declare(strict_types=1);

namespace App\Application\Query\Event\GetEvents;

use App\Infrastructure\Share\Bridge\ApiPlatform\DataProvider\ElasticsearchDataProvider;
use App\Infrastructure\Share\Bus\Query\QueryHandlerInterface;

class GetEventsHandler implements QueryHandlerInterface
{
    private ElasticsearchDataProvider $provider;

    public function __construct(ElasticsearchDataProvider $provider)
    {
        $this->provider = $provider;
    }

    public function __invoke(GetEventsQuery $data): iterable
    {
        return $this->provider->getCollection(
            $data->resourceClass(),
            $data->collectionOperationName(),
            $data->context()
        );
    }
}
