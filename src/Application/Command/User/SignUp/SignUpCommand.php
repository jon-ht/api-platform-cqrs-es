<?php

declare(strict_types=1);

namespace App\Application\Command\User\SignUp;

class SignUpCommand
{
    private string $uuid;

    private object $credentials;

    public function __construct(string $uuid, string $email, string $plainPassword)
    {
        $this->uuid = $uuid;
        $this->credentials = (object) ['email' => $email, 'password' => $plainPassword];
    }

    public function uuid(): string
    {
        return $this->uuid;
    }

    public function credentials(): object
    {
        return $this->credentials;
    }
}
