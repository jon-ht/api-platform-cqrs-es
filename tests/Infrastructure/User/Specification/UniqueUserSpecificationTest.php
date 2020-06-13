<?php

declare(strict_types=1);

namespace App\Tests\Infrastructure\User\Specification;

use App\Domain\Shared\Exception\NonUniqueUuidException;
use App\Domain\Shared\Specification\UniqueAggregateRootSpecificationInterface;
use App\Domain\User\Exception\EmailAlreadyExistException;
use App\Domain\User\Specification\UniqueEmailSpecificationInterface;
use App\Domain\User\ValueObject\Auth\Credentials;
use App\Domain\User\ValueObject\Auth\HashedPassword;
use App\Domain\User\ValueObject\Email;
use App\Infrastructure\User\Specification\UniqueUserSpecification;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class UniqueUserSpecificationTest extends TestCase
{
    private const EMAIL = 'email@domain.com';

    private const PASSWORD = 'password';

    /** @var UniqueAggregateRootSpecificationInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $uniqueAggregateRootSpecification;

    /** @var UniqueEmailSpecificationInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $uniqueEmailSpecification;

    protected function setUp(): void
    {
        parent::setUp();

        $this->uniqueAggregateRootSpecification = $this->createMock(UniqueAggregateRootSpecificationInterface::class);
        $this->uniqueEmailSpecification = $this->createMock(UniqueEmailSpecificationInterface ::class);
    }

    /**
     * @test
     *
     * @group unit
     */
    public function given_a_non_unique_uuid_it_should_throw_an_exception(): void
    {
        $this->expectException(NonUniqueUuidException::class);

        $this->uniqueAggregateRootSpecification->method('isUnique')
            ->willThrowException(new NonUniqueUuidException());

        $this->uniqueEmailSpecification->method('isUnique')
            ->willReturn(true);

        $specification = new UniqueUserSpecification(
            $this->uniqueAggregateRootSpecification,
            $this->uniqueEmailSpecification
        );

        $specification->isUnique(
            Uuid::uuid4(),
            new Credentials(
                Email::fromString(self::EMAIL),
                HashedPassword::encode(self::PASSWORD)
            )
        );
    }

    /**
     * @test
     *
     * @group unit
     */
    public function given_a_non_unique_email_it_should_throw_an_exception(): void
    {
        $this->expectException(EmailAlreadyExistException::class);

        $this->uniqueAggregateRootSpecification->method('isUnique')
            ->willReturn(true);

        $this->uniqueEmailSpecification->method('isUnique')
            ->willThrowException(new EmailAlreadyExistException());

        $specification = new UniqueUserSpecification(
            $this->uniqueAggregateRootSpecification,
            $this->uniqueEmailSpecification
        );

        $specification->isUnique(
            Uuid::uuid4(),
            new Credentials(
                Email::fromString(self::EMAIL),
                HashedPassword::encode(self::PASSWORD)
            )
        );
    }

    /**
     * @test
     *
     * @group unit
     */
    public function given_a_unique_uuid_and_email_it_should_return_true(): void
    {
        $this->uniqueAggregateRootSpecification->method('isUnique')
            ->willReturn(true);

        $this->uniqueEmailSpecification->method('isUnique')
            ->willReturn(true);

        $specification = new UniqueUserSpecification(
            $this->uniqueAggregateRootSpecification,
            $this->uniqueEmailSpecification
        );

        self::assertTrue($specification->isUnique(
            Uuid::uuid4(),
            new Credentials(
                Email::fromString(self::EMAIL),
                HashedPassword::encode(self::PASSWORD)
            )
        ));
    }
}
