<?php

declare(strict_types=1);

namespace App\Application\Command\User\ChangeEmail;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * @psalm-suppress MissingConstructor
 */
class ChangeEmailInput
{
    /**
     * @Assert\Email
     * @Assert\NotBlank
     */
    public string $email;
}
