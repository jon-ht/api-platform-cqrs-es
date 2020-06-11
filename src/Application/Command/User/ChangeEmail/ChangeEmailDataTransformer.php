<?php

declare(strict_types=1);

namespace App\Application\Command\User\ChangeEmail;

use App\Application\Command\CommandInputTransformer;
use InvalidArgumentException;

class ChangeEmailDataTransformer extends CommandInputTransformer
{
    protected function create($object, string $to, array $context = []): ChangeEmailCommand
    {
        if (!$object instanceof ChangeEmailInput) {
            throw new InvalidArgumentException(\sprintf('Object is not an instance of %s', ChangeEmailInput::class));
        }

        return new ChangeEmailCommand($this->getUuid(), $object->email);
    }

    protected function commandClass(): string
    {
        return ChangeEmailCommand::class;
    }

    protected function commandInputClass(): string
    {
        return ChangeEmailInput::class;
    }
}
