<?php

declare(strict_types=1);

namespace App\Domain\User\ValueObject\Auth;

use ApiPlatform\Core\Bridge\Symfony\Validator\Exception\ValidationException;
use App\Domain\Shared\ValueObject\ValidationAwareTrait;
use App\Domain\User\Validator\Constraints as AssertDomain;

final class HashedPassword
{
    use ValidationAwareTrait;

    private string $hashedPassword;

    public const COST = 12;

    private function __construct(string $hashedPassword)
    {
        $this->hashedPassword = $hashedPassword;
    }

    /**
     * @throws ValidationException
     * @throws \RuntimeException
     */
    public static function encode(string $plainPassword): self
    {
        return new self(self::hash($plainPassword));
    }

    public static function fromHash(string $hashedPassword): self
    {
        return new self($hashedPassword);
    }

    public function match(string $plainPassword): bool
    {
        return \password_verify($plainPassword, $this->hashedPassword);
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

    public function toString(): string
    {
        return $this->hashedPassword;
    }

    public function __toString(): string
    {
        return $this->hashedPassword;
    }
}
