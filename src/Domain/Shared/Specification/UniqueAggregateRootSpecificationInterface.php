<?php

declare(strict_types=1);

namespace App\Domain\Shared\Specification;

use App\Domain\Shared\Exception\NonUniqueUuidException;
use Ramsey\Uuid\UuidInterface;

interface UniqueAggregateRootSpecificationInterface
{
    /**
     * @throws NonUniqueUuidException
     */
    public function isUnique(UuidInterface $uuid): bool;
}
