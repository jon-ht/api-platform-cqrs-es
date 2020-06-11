<?php

declare(strict_types=1);

namespace App\Application\Command;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use ApiPlatform\Core\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\RequestStack;

abstract class CommandInputTransformer implements DataTransformerInterface
{
    protected RequestStack $requestStack;

    private ValidatorInterface $validator;

    public function __construct(RequestStack $requestStack, ValidatorInterface $validator)
    {
        $this->requestStack = $requestStack;
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

    protected function getUuid(): string
    {
        if (!$request = $this->requestStack->getCurrentRequest()) {
            throw new \InvalidArgumentException('No current request available');
        }

        if (!$uuid = $request->attributes->get('uuid')) {
            throw new \InvalidArgumentException('Unable to find user uuid in request params');
        }

        return $uuid;
    }
}
