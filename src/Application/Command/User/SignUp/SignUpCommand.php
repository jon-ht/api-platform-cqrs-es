<?php

declare(strict_types=1);

namespace App\Application\Command\User\SignUp;

use App\Domain\User\ValueObject\Auth\Credentials;
use App\Domain\User\ValueObject\Auth\HashedPassword;
use App\Domain\User\ValueObject\Email;
use App\Infrastructure\Share\Bus\Command\CommandInterface;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class SignUpCommand implements CommandInterface
{
    private UuidInterface $uuid;

    private Credentials $credentials;

    public function __construct(string $uuid, string $email, string $plainPassword)
    {
        $this->uuid = Uuid::fromString($uuid);
        $this->credentials = new Credentials(Email::fromString($email), HashedPassword::encode($plainPassword));
    }

    public function uuid(): UuidInterface
    {
        return $this->uuid;
    }

    public function credentials(): Credentials
    {
        return $this->credentials;
    }
}
