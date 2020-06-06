<?php

declare(strict_types=1);

namespace App\Infrastructure\Share\Event\Consumer;

use App\Infrastructure\Share\Bus\Event\Event;
use App\Infrastructure\Share\Bus\Event\EventHandlerInterface;
use App\Infrastructure\Share\Event\Persistence\DomainMessageElasticsearchStore;

class SendEventsToElasticConsumer implements EventHandlerInterface
{
    private DomainMessageElasticsearchStore $elasticsearchStore;

    public function __construct(DomainMessageElasticsearchStore $elasticsearchStore)
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
