<?php

declare(strict_types=1);

namespace App\Application\Command\User\ChangeEmail;

use App\Application\Command\CommandInputTransformer;
use InvalidArgumentException;
use Ramsey\Uuid\UuidInterface;

class ChangeEmailDataTransformer extends CommandInputTransformer
{
    protected function create($object, string $to, array $context = []): ChangeEmailCommand
    {
        if (!$object instanceof ChangeEmailInput) {
            throw new InvalidArgumentException(\sprintf('Object is not an instance of %s', ChangeEmailInput::class));
        }

        if (!isset($context['uuid'])) {
            throw new \RuntimeException(\sprintf('Missing uuid value in context'));
        }

        if (($uuid = $context['uuid']) && !$uuid instanceof UuidInterface) {
            throw new \InvalidArgumentException(\sprintf('Given uuid must be an instance of %s', UuidInterface::class));
        }

        return new ChangeEmailCommand($uuid->toString(), $object->email);
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
