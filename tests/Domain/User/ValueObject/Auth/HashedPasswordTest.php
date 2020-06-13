<?php

declare(strict_types=1);

namespace App\Tests\Domain\User\ValueObject\Auth;

use ApiPlatform\Core\Bridge\Symfony\Validator\Exception\ValidationException;
use App\Domain\User\ValueObject\Auth\HashedPassword;
use PHPUnit\Framework\TestCase;

class HashedPasswordTest extends TestCase
{
    /**
     * @test
     *
     * @group unit
     */
    public function encoded_password_should_be_validated(): void
    {
        $pass = HashedPassword::encode('1234567890');

        self::assertTrue($pass->match('1234567890'));
    }

    /**
     * @test
     *
     * @group unit
     */
    public function min_6_password_length(): void
    {
        $this->expectException(ValidationException::class);

        HashedPassword::encode('12345');
    }

    /**
     * @test
     *
     * @group unit
     */
    public function from_hash_password_should_still_valid(): void
    {
        $pass = HashedPassword::fromHash((string) HashedPassword::encode('1234567890'));

        self::assertTrue($pass->match('1234567890'));
    }
}
