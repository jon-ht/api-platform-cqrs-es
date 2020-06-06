<?php

declare(strict_types=1);

namespace App\Domain\Shared\Specification;

use Ramsey\Uuid\UuidInterface;

interface UniqueAggregateRootSpecificationInterface
{
    public function isUnique(UuidInterface $uuid): bool;
}
