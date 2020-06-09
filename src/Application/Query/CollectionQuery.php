<?php

declare(strict_types=1);

namespace App\Application\Query;

use ApiPlatform\Core\Api\OperationType;
use App\Infrastructure\Share\Bus\Query\QueryInterface;

/**
 * Ensure minimum context is set when QueryHandler depends on API Platform data providers
 *
 * @see CollectionQueryFactory::createCollectionQuery
 */
abstract class CollectionQuery implements QueryInterface
{
    protected array $context;

    public function __construct(array $context)
    {
        $this->context = $context;
    }

    public function context(): array
    {
        return \array_merge([
            'operation_type' => static::operationType(),
            'collection_operation_name' => static::collectionOperationName(),
            'resource_class' => static::resourceClass(),
        ], $this->context);
    }

    abstract public static function createWithContext(array $context): self;

    abstract public static function resourceClass(): string;

    public static function collectionOperationName(): string
    {
        return 'get';
    }

    public static function operationType(): string
    {
        return OperationType::COLLECTION;
    }
}
