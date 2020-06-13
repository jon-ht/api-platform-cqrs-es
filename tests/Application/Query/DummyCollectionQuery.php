<?php

declare(strict_types=1);

namespace App\Tests\Application\Query;

use App\Application\Query\CollectionQuery;

class DummyCollectionQuery extends CollectionQuery
{
    public static function createWithContext(array $context): CollectionQuery
    {
        return new self($context);
    }

    public static function resourceClass(): string
    {
        return 'Foo';
    }
}
