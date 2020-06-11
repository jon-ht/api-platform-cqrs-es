<?php

declare(strict_types=1);

namespace App\UI\Http\Rest\Controller\User;

use App\Application\Query\User\FindByEmail\FindByEmailQuery;
use App\UI\Http\Rest\Controller\QueryController;

final class GetUserByEmailController extends QueryController
{
    /**
     * @return mixed
     *
     * @throws \Throwable
     */
    public function __invoke(string $email)
    {
        $command = new FindByEmailQuery($email);

        return $this->ask($command);
    }
}
