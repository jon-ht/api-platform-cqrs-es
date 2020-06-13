<?php

declare(strict_types=1);

namespace App\Tests\Infrastructure\User\Specification;

use App\Domain\User\Exception\EmailAlreadyExistException;
use App\Domain\User\Repository\CheckUserByEmailInterface;
use App\Domain\User\ValueObject\Email;
use App\Infrastructure\User\Specification\UniqueEmailSpecification;
use Doctrine\ORM\NonUniqueResultException;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class UniqueEmailSpecificationTest extends TestCase
{
    /** @var CheckUserByEmailInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $checkUserByEmail;

    protected function setUp(): void
    {
        parent::setUp();

        $this->checkUserByEmail = $this->createMock(CheckUserByEmailInterface::class);
    }

    /**
     * @test
     *
     * @group unit
     */
    public function given_an_existing_email_it_should_throw_an_exception(): void
    {
        $this->expectException(EmailAlreadyExistException::class);

        $this->checkUserByEmail->method('existsEmail')
            ->willReturn(Uuid::uuid4());

        $specification = new UniqueEmailSpecification($this->checkUserByEmail);
        $specification->isUnique(Email::fromString('email@domain.com'));
    }

    /**
     * @test
     *
     * @group unit
     */
    public function given_a_non_unique_result_exception_it_should_throw_an_exception(): void
    {
        $this->expectException(EmailAlreadyExistException::class);

        $this->checkUserByEmail->method('existsEmail')
            ->willThrowException(new NonUniqueResultException());

        $specification = new UniqueEmailSpecification($this->checkUserByEmail);
        $specification->isUnique(Email::fromString('email@domain.com'));
    }

    /**
     * @test
     *
     * @group unit
     */
    public function given_a_unique_email_it_should_return_true(): void
    {
        $this->checkUserByEmail->method('existsEmail')
            ->willReturn(null);

        $specification = new UniqueEmailSpecification($this->checkUserByEmail);

        self::assertTrue($specification->isUnique(Email::fromString('email@domain.com')));
    }
}
