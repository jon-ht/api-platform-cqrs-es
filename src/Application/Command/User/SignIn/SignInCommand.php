<?php

declare(strict_types=1);

namespace App\Application\Command\User\SignIn;

use ApiPlatform\Core\Bridge\Symfony\Validator\Exception\ValidationException;
use App\Domain\User\ValueObject\Email;
use App\Infrastructure\Share\Bus\Command\CommandInterface;

class SignInCommand implements CommandInterface
{
    private Email $email;

    private string $plainPassword;

    /**
     * @throws ValidationException
     */
    public function __construct(string $email, string $plainPassword)
    {
        $this->email = Email::fromString($email);
        $this->plainPassword = $plainPassword;
    }

    public function email(): Email
    {
        return $this->email;
    }

    public function plainPassword(): string
    {
        return $this->plainPassword;
    }
}
