<?php

declare(strict_types=1);

namespace App\Application\Command\User\SignUp;

use App\Domain\User\Validator\Constraints as AssertDomain;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @psalm-suppress MissingConstructor
 */
class SignUpInput
{
    /**
     * @Assert\Uuid
     * @Assert\NotBlank
     */
    public string $uuid;

    /**
     * @Assert\Email
     * @Assert\NotBlank
     */
    public string $email;

    /** @AssertDomain\Auth\Password */
    public string $password;
}
