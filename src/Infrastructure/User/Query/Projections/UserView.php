<?php

declare(strict_types=1);

namespace App\Infrastructure\User\Query\Projections;

use App\Domain\Shared\ValueObject\DateTime;
use App\Domain\User\Event\UserWasCreated;
use App\Domain\User\ValueObject\Auth\Credentials;
use App\Domain\User\ValueObject\Email;
use Ramsey\Uuid\UuidInterface;

class UserView
{
    private UuidInterface $uuid;

    private Credentials $credentials;

    private DateTime $createdAt;

    private ?DateTime $updatedAt;

    private function __construct(
        UuidInterface $uuid,
        Credentials $credentials,
        DateTime $createdAt,
        ?DateTime $updatedAt
    ) {
        $this->uuid = $uuid;
        $this->credentials = $credentials;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    public static function fromUserWasCreated(UserWasCreated $event): self
    {
        return new self(
            $event->uuid,
            $event->credentials,
            $event->createdAt,
            null
        );
    }

    public function uuid(): UuidInterface
    {
        return $this->uuid;
    }

    public function credentials(): Credentials
    {
        return $this->credentials;
    }

    public function createdAt(): DateTime
    {
        return $this->createdAt;
    }

    public function updatedAt(): ?DateTime
    {
        return $this->updatedAt;
    }

    public function email(): string
    {
        return (string) $this->credentials->email;
    }

    public function encodedPassword(): string
    {
        return (string) $this->credentials->password;
    }

    public function changeEmail(Email $email): void
    {
        $this->credentials->email = $email;
    }

    public function changeUpdatedAt(DateTime $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }
}
