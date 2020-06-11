<?php

declare(strict_types=1);

namespace App\UI\Http\Rest\Controller;

use App\Infrastructure\Share\Bus\Command\CommandBus;
use App\Infrastructure\Share\Bus\Command\CommandInterface;
use App\Infrastructure\Share\Bus\Query\QueryBus;

abstract class CommandQueryController extends QueryController
{
    private CommandBus $commandBus;

    public function __construct(
        CommandBus $commandBus,
        QueryBus $queryBus
    ) {
        parent::__construct($queryBus);

        $this->commandBus = $commandBus;
    }

    /**
     * @throws \Throwable
     */
    protected function exec(CommandInterface $command): void
    {
        $this->commandBus->handle($command);
    }
}
