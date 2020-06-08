<?php

declare(strict_types=1);

namespace App\Application\Query\Event\GetEvents;

use App\Application\Query\CollectionQuery;
use App\Domain\Shared\DomainEvent;

class GetEventsQuery extends CollectionQuery
{
    public static function createWithContext(array $context): self
    {
        return new self($context);
    }

    public static function resourceClass(): string
    {
        return DomainEvent::class;
    }
}
