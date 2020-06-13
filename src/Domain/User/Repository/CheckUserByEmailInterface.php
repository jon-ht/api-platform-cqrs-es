<?php

declare(strict_types=1);

namespace App\Domain\User\Repository;

use App\Domain\User\ValueObject\Email;

interface CheckUserByEmailInterface
{
    public function emailExists(Email $email): bool;
}
