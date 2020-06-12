<?php

declare(strict_types=1);

namespace App\Infrastructure\User\Specification;

use App\Domain\Shared\Specification\UniqueAggregateRootSpecificationInterface;
use App\Domain\User\Specification\UniqueEmailSpecificationInterface;
use App\Domain\User\Specification\UniqueUserSpecificationInterface;
use App\Domain\User\ValueObject\Auth\Credentials;
use Ramsey\Uuid\UuidInterface;

class UniqueUserSpecification implements UniqueUserSpecificationInterface
{
    private UniqueEmailSpecificationInterface $uniqueEmailSpecification;

    private UniqueAggregateRootSpecificationInterface $uniqueAggregateRootSpecification;

    public function __construct(
        UniqueAggregateRootSpecificationInterface $uniqueAggregateRootSpecification,
        UniqueEmailSpecificationInterface $uniqueEmailSpecification
    ) {
        $this->uniqueAggregateRootSpecification = $uniqueAggregateRootSpecification;
        $this->uniqueEmailSpecification = $uniqueEmailSpecification;
    }

    public function isUnique(UuidInterface $uuid, Credentials $credentials): bool
    {
        return $this->uniqueAggregateRootSpecification->isUnique($uuid)
            && $this->uniqueEmailSpecification->isUnique($credentials->email);
    }
}
