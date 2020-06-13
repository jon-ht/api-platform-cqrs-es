<?php

declare(strict_types=1);

namespace App\Tests\Domain\User\ValueObject;

use ApiPlatform\Core\Bridge\Symfony\Validator\Exception\ValidationException;
use App\Domain\User\ValueObject\Email;
use PHPUnit\Framework\TestCase;

class EmailTest extends TestCase
{
    /**
     * @test
     *
     * @group unit
     */
    public function invalid_email_should_throw_an_exception(): void
    {
        $this->expectException(ValidationException::class);

        Email::fromString('invalid email');
    }

    /**
     * @test
     *
     * @group unit
     */
    public function empty_email_should_throw_an_exception(): void
    {
        $this->expectException(ValidationException::class);

        Email::fromString('');
    }

    /**
     * @test
     *
     * @group unit
     */
    public function valid_email_should_be_able_to_convert_to_string(): void
    {
        $email = Email::fromString('email@domain.com');

        self::assertSame('email@domain.com', $email->toString());
        self::assertSame('email@domain.com', (string) $email);
    }
}
