<?php

declare(strict_types=1);

namespace App\Domain\User\ValueObject\Auth;

use ApiPlatform\Core\Bridge\Symfony\Validator\Exception\ValidationException;
use App\Domain\Shared\ValueObject\AbstractString;
use App\Domain\Shared\ValueObject\ValidationAwareTrait;
use App\Domain\User\Validator\Constraints as AssertDomain;

/**
 * @method static HashedPassword fromString(string $value)
 */
final class HashedPassword extends AbstractString
{
    use ValidationAwareTrait;

    public const COST = 12;

    /**
     * @throws ValidationException
     * @throws \RuntimeException
     */
    public static function encode(string $plainPassword): self
    {
        return self::fromString(self::hash($plainPassword));
    }

    public static function fromHash(string $hashedPassword): self
    {
        return static::fromString($hashedPassword);
    }

    public function match(string $plainPassword): bool
    {
        return \password_verify($plainPassword, $this->value);
    }

    protected static function create(string $value): AbstractString
    {
        return new self($value);
    }

    /**
     * @throws ValidationException
     * @throws \RuntimeException
     */
    private static function hash(string $plainPassword): string
    {
        static::validate($plainPassword, new AssertDomain\Auth\Password());

        /** @var string|bool|null $hashedPassword */
        $hashedPassword = \password_hash($plainPassword, \PASSWORD_BCRYPT, ['cost' => self::COST]);

        if (\is_bool($hashedPassword)) {
            throw new \RuntimeException('Server error hashing password');
        }

        return (string) $hashedPassword;
    }
}
