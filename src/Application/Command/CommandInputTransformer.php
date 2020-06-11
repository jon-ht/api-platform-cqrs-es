<?php

declare(strict_types=1);

namespace App\Application\Command;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use ApiPlatform\Core\Validator\ValidatorInterface;

abstract class CommandInputTransformer implements DataTransformerInterface
{
    private ValidatorInterface $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        // In the case of an input, the value given here is an array (the JSON decoded).
        // if it's a command we transformed the data already
        if (\is_a($data, $this->commandClass(), true)) {
            return false;
        }

        return $this->commandInputClass() === ($context['input']['class'] ?? null);
    }

    public function transform($object, string $to, array $context = [])
    {
        $this->validator->validate($object);

        return $this->create($object, $to, $context);
    }

    /**
     * @param mixed $object
     *
     * @return object
     */
    abstract protected function create($object, string $to, array $context = []);

    abstract protected function commandClass(): string;

    abstract protected function commandInputClass(): string;
}
