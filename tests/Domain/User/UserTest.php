<?php

declare(strict_types=1);

namespace App\Tests\Domain\User;

use App\Domain\Shared\Exception\DateTimeException;
use App\Domain\User\Event\UserEmailChanged;
use App\Domain\User\Event\UserSignedIn;
use App\Domain\User\Event\UserWasCreated;
use App\Domain\User\Exception\EmailAlreadyExistException;
use App\Domain\User\Exception\InvalidCredentialsException;
use App\Domain\User\Specification\UniqueEmailSpecificationInterface;
use App\Domain\User\Specification\UniqueUserSpecificationInterface;
use App\Domain\User\User;
use App\Domain\User\ValueObject\Auth\Credentials;
use App\Domain\User\ValueObject\Auth\HashedPassword;
use App\Domain\User\ValueObject\Email;
use Broadway\Domain\DomainMessage;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class UserTest extends TestCase
{
    private const EMAIL = 'email@domain.com';

    private const PASSWORD = 'password';

    private UuidInterface $uuid;

    /** @var UniqueUserSpecificationInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $uniqueUserSpecification;

    protected function setUp(): void
    {
        parent::setUp();

        $this->uuid = Uuid::uuid4();
        $this->uniqueUserSpecification = $this->createMock(UniqueUserSpecificationInterface::class);
        $this->uniqueUserSpecification
            ->method('isUnique')
            ->willReturn(true);
    }

    /**
     * @test
     *
     * @group unit
     *
     * @throws DateTimeException
     */
    public function given_a_valid_email_it_should_create_a_user_instance(): void
    {
        $user = $this->createUser();

        self::assertSame(self::EMAIL, $user->email());
        self::assertSame($this->uuid->toString(), $user->uuid());

        $events = $user->getUncommittedEvents();

        self::assertCount(1, $events->getIterator(), 'Only one event should be in the buffer');

        /** @var DomainMessage $event */
        $event = $events->getIterator()->current();

        self::assertInstanceOf(UserWasCreated::class, $event->getPayload(), 'First event should be UserWasCreated');
    }

    /**
     * @test
     *
     * @group unit
     *
     * @throws DateTimeException
     */
    public function given_a_new_email_it_should_change_if_not_eq_to_prev_email(): void
    {
        $uniqueEmailSpecification = $this->createMock(UniqueEmailSpecificationInterface::class);
        $uniqueEmailSpecification
            ->method('isUnique')
            ->willReturn(true);

        $user = $this->createUser();

        $newEmail = 'second_email@domain.com';

        $user->changeEmail(Email::fromString($newEmail), $uniqueEmailSpecification);

        self::assertSame($user->email(), $newEmail, 'Emails should be equals');

        $events = $user->getUncommittedEvents();

        self::assertCount(2, $events->getIterator(), '2 event should be in the buffer');

        /** @var DomainMessage $event */
        $event = $events->getIterator()->offsetGet(1);

        self::assertInstanceOf(UserEmailChanged::class, $event->getPayload(), 'Second event should be UserEmailChanged');
    }

    /**
     * @test
     *
     * @group unit
     *
     * @throws DateTimeException
     */
    public function given_a_registered_email_it_should_throw_an_email_already_exists_exception(): void
    {
        $this->expectException(EmailAlreadyExistException::class);

        $uniqueEmailSpecification = $this->createMock(UniqueEmailSpecificationInterface::class);
        $uniqueEmailSpecification
            ->method('isUnique')
            ->willThrowException(new EmailAlreadyExistException());

        $user = $this->createUser();

        $newEmail = 'second_email@domain.com';

        $user->changeEmail(Email::fromString($newEmail), $uniqueEmailSpecification);
    }

    /**
     * @test
     *
     * @group unit
     *
     * @throws DateTimeException
     */
    public function given_a_new_email_when_email_changes_should_update_the_update_at_field(): void
    {
        $uniqueEmailSpecification = $this->createMock(UniqueEmailSpecificationInterface::class);
        $uniqueEmailSpecification
            ->method('isUnique')
            ->willReturn(true);

        $user = $this->createUser();

        self::assertNotNull($user->createdAt());
        self::assertNull($user->updatedAt());

        $initialUpdatedAt = $user->updatedAt();
        \usleep(1000);
        $newEmail = 'second_emain@domain.com';
        $user->changeEmail(Email::fromString($newEmail), $uniqueEmailSpecification);

        self::assertNotSame($user->updatedAt(), $initialUpdatedAt);
    }

    /**
     * @test
     *
     * @group unit
     *
     * @throws DateTimeException
     */
    public function given_a_plain_password_it_should_sign_in(): void
    {
        $user = $this->createUser();

        $user->signIn(self::PASSWORD);

        $events = $user->getUncommittedEvents();

        self::assertCount(2, $events->getIterator(), '2 event should be in the buffer');

        /** @var DomainMessage $event */
        $event = $events->getIterator()->offsetGet(1);

        self::assertInstanceOf(UserSignedIn::class, $event->getPayload(), 'Second event should be UserSignedIn');
    }

    /**
     * @test
     *
     * @group unit
     *
     * @throws DateTimeException
     */
    public function given_an_invalid_plain_password_it_should_throw_an_exception(): void
    {
        $this->expectException(InvalidCredentialsException::class);

        $user = $this->createUser();

        $user->signIn('invalid password');
    }

    /**
     * @throws DateTimeException
     */
    protected function createUser(): User
    {
        return User::create(
            $this->uuid,
            new Credentials(
                Email::fromString(self::EMAIL),
                HashedPassword::encode(self::PASSWORD)
            ),
            $this->uniqueUserSpecification
        );
    }
}
