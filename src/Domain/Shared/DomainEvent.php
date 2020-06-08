<?php

declare(strict_types=1);

namespace App\Domain\Shared;

class DomainEvent
{
    private string $id;

    private string $type;

    /** @var mixed */
    private $payload;

    private string $occurredOn;

    /**
     * @param mixed $payload
     */
    public function __construct(
        string $id,
        string $type,
        $payload,
        string $occurredOn
    ) {
        $this->id = $id;
        $this->type = $type;
        $this->payload = $payload;
        $this->occurredOn = $occurredOn;
    }

    public function id(): string
    {
        return $this->id;
    }

    public function type(): string
    {
        return $this->type;
    }

    /**
     * @return mixed
     */
    public function payload()
    {
        return $this->payload;
    }

    public function occurredOn(): string
    {
        return $this->occurredOn;
    }
}
