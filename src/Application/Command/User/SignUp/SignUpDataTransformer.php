<?php

declare(strict_types=1);

namespace App\Application\Command\User\SignUp;

use App\Application\Command\CommandInputTransformer;

class SignUpDataTransformer extends CommandInputTransformer
{
    protected function create($object, string $to, array $context = []): SignUpCommand
    {
        if (!$object instanceof SignUpInput) {
            throw new \InvalidArgumentException(\sprintf('Object is not an instance of %s', SignUpInput::class));
        }

        return new SignUpCommand(
            $object->uuid,
            $object->email,
            $object->password
        );
    }

    protected function commandClass(): string
    {
        return SignUpCommand::class;
    }

    protected function commandInputClass(): string
    {
        return SignUpInput::class;
    }
}
