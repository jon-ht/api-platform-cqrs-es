<?php

declare(strict_types=1);

namespace App\Infrastructure\User\Doctrine;

use App\Domain\User\ValueObject\Auth\HashedPassword;
use App\Infrastructure\Share\Doctrine\AbstractStringType;

class HashedPasswordType extends AbstractStringType
{
    protected function getValueObjectClassName(): string
    {
        return HashedPassword::class;
    }
}
