<?php

declare(strict_types=1);

namespace App\Infrastructure\Share\Event\Consumer;

use App\Infrastructure\Share\Bus\Event\Event;
use App\Infrastructure\Share\Bus\Event\EventHandlerInterface;
use App\Infrastructure\Share\Event\Query\DomainEventElasticsearchRepository;

class SendEventsToElasticConsumer implements EventHandlerInterface
{
    private DomainEventElasticsearchRepository $elasticsearchStore;

    public function __construct(DomainEventElasticsearchRepository $elasticsearchStore)
    {
        $this->elasticsearchStore = $elasticsearchStore;
    }

    public function __invoke(Event $event): void
    {
        $this->elasticsearchStore->store(
            $event->getDomainMessage()
        );
    }
}
