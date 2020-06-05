<?php

declare(strict_types=1);

namespace App\Application\Command\User\SignUp;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * @psalm-suppress MissingConstructor
 */
class SignUpInput
{
    /** @Assert\Uuid */
    public string $uuid;

    /** @Assert\Email */
    public string $email;

    public string $password;
}
