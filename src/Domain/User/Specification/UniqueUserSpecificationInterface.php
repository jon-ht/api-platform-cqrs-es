<?php

declare(strict_types=1);

namespace App\Domain\User\Specification;

use App\Domain\Shared\Exception\NonUniqueUuidException;
use App\Domain\User\Exception\EmailAlreadyExistException;
use App\Domain\User\ValueObject\Auth\Credentials;
use Ramsey\Uuid\UuidInterface;

interface UniqueUserSpecificationInterface
{
    /**
     * @throws NonUniqueUuidException
     * @throws EmailAlreadyExistException
     */
    public function isUnique(UuidInterface $uuid, Credentials $credentials): bool;
}
