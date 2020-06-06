<?php

declare(strict_types=1);

namespace App\Domain\User\ValueObject;

use ApiPlatform\Core\Bridge\Symfony\Validator\Exception\ValidationException;
use App\Domain\Shared\ValueObject\AbstractString;
use App\Domain\Shared\ValueObject\ValidationAwareTrait;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @method static Email fromString(string $value)
 */
class Email extends AbstractString
{
    use ValidationAwareTrait;

    /**
     * @throws ValidationException
     */
    protected static function create(string $value): self
    {
        static::validate($value, [
            new Assert\NotBlank(),
            new Assert\Email(),
        ]);

        return new self($value);
    }
}
