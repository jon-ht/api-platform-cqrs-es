<?php

declare(strict_types=1);

namespace App\Application\Query\User\FindByEmail;

use ApiPlatform\Core\Bridge\Symfony\Validator\Exception\ValidationException;
use App\Domain\User\ValueObject\Email;
use App\Infrastructure\Share\Bus\Query\QueryInterface;

class FindByEmailQuery implements QueryInterface
{
    private Email $email;

    /**
     * @throws ValidationException
     */
    public function __construct(string $email)
    {
        $this->email = Email::fromString($email);
    }

    public function email(): Email
    {
        return $this->email;
    }
}
