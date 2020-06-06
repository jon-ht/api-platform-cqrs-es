<?php

declare(strict_types=1);

namespace App\Domain\User\Factory;

use App\Domain\Shared\Specification\UniqueAggregateRootSpecificationInterface;
use App\Domain\Shared\ValueObject\DateTime;
use App\Domain\User\Event\UserWasCreated;
use App\Domain\User\Specification\UniqueEmailSpecificationInterface;
use App\Domain\User\User;
use App\Domain\User\ValueObject\Auth\Credentials;
use Ramsey\Uuid\UuidInterface;

class UserFactory
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

    /**
     * @throws \App\Domain\Shared\Exception\DateTimeException
     */
    public function create(UuidInterface $uuid, Credentials $credentials): User
    {
        $this->uniqueAggregateRootSpecification->isUnique($uuid);
        $this->uniqueEmailSpecification->isUnique($credentials->email);

        $user = new User();

        $user->apply(new UserWasCreated($uuid, $credentials, DateTime::now()));

        return $user;
    }
}
