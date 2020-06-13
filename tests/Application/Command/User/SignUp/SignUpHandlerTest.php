<?php

declare(strict_types=1);

namespace App\Tests\Application\Command\User\SignUp;

use App\Application\Command\User\SignUp\SignUpCommand;
use App\Domain\User\Event\UserWasCreated;
use App\Tests\Application\ApplicationTestCase;
use App\Tests\Infrastructure\Share\Event\EventCollectorListener;
use Broadway\Domain\DomainMessage;
use Ramsey\Uuid\Uuid;

class SignUpHandlerTest extends ApplicationTestCase
{
    /**
     * @test
     *
     * @group integration
     */
    public function command_handler_must_fire_domain_event(): void
    {
        $uuid = Uuid::uuid4();
        $email = 'email@domain.com';

        $command = new SignUpCommand($uuid->toString(), $email, 'password');
        $this->handle($command);

        /** @var EventCollectorListener $collector */
        $collector = $this->service(EventCollectorListener::class);

        /** @var DomainMessage[] $events */
        $events = $collector->popEvents();

        self::assertCount(1, $events);

        /** @var UserWasCreated $userCreatedEvent */
        $userCreatedEvent = $events[0]->getPayload();

        self::assertInstanceOf(UserWasCreated::class, $userCreatedEvent);
    }
}
