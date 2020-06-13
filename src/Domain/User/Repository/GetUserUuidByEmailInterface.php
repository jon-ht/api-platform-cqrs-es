<?php

declare(strict_types=1);

namespace App\Domain\User\Repository;

use App\Domain\User\ValueObject\Email;
use Ramsey\Uuid\UuidInterface;

interface GetUserUuidByEmailInterface
{
    public function getUuidByEmail(Email $email): ?UuidInterface;
}
