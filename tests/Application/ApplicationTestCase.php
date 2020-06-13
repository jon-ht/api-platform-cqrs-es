<?php

declare(strict_types=1);

namespace App\Tests\Application;

use App\Infrastructure\Share\Bus\Command\CommandBus;
use App\Infrastructure\Share\Bus\Command\CommandInterface;
use App\Infrastructure\Share\Bus\Query\QueryBus;
use App\Infrastructure\Share\Bus\Query\QueryInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\TerminateEvent;
use Symfony\Component\HttpKernel\KernelEvents;

abstract class ApplicationTestCase extends KernelTestCase
{
    private ?CommandBus $commandBus;

    private ?QueryBus $queryBus;

    protected function setUp(): void
    {
        self::bootKernel();

        $this->commandBus = $this->service(CommandBus::class);
        $this->queryBus = $this->service(QueryBus::class);
    }

    /**
     * @return mixed
     */
    protected function ask(QueryInterface $query)
    {
        return $this->queryBus->handle($query);
    }

    protected function handle(CommandInterface $command): void
    {
        $this->commandBus->handle($command);
    }

    /**
     * @return object|null
     */
    protected function service(string $serviceId)
    {
        return self::$container->get($serviceId);
    }

    protected function fireTerminateEvent(): void
    {
        /** @var EventDispatcher $dispatcher */
        $dispatcher = $this->service('event_dispatcher');

        $dispatcher->dispatch(
            new TerminateEvent(
                static::$kernel,
                Request::create('/'),
                new Response()
            ),
            KernelEvents::TERMINATE
        );
    }

    protected function tearDown(): void
    {
        $this->commandBus = null;
        $this->queryBus = null;
    }
}
