<?php

declare(strict_types=1);

namespace App\Tests\Application\Query\Event\GetEvents;

use ApiPlatform\Core\Bridge\Elasticsearch\DataProvider\Paginator;
use App\Application\Command\User\SignUp\SignUpCommand;
use App\Application\Query\Event\GetEvents\GetEventsQuery;
use App\Domain\Shared\DomainEvent;
use App\Domain\Shared\ValueObject\DateTime as DomainDateTime;
use App\Domain\User\Event\UserWasCreated;
use App\Domain\User\ValueObject\Auth\Credentials;
use App\Domain\User\ValueObject\Auth\HashedPassword;
use App\Domain\User\ValueObject\Email;
use App\Infrastructure\Share\Bus\Event\Event;
use App\Infrastructure\Share\Event\Consumer\SendEventsToElasticConsumer;
use App\Infrastructure\Share\Event\Query\DomainEventElasticsearchRepository;
use App\Tests\Application\ApplicationTestCase;
use Broadway\Domain\DateTime;
use Broadway\Domain\DomainMessage;
use Broadway\Domain\Metadata;
use Ramsey\Uuid\Uuid;

final class GetEventsTest extends ApplicationTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        /** @var DomainEventElasticsearchRepository $eventReadStore */
        $eventReadStore = $this->service(DomainEventElasticsearchRepository::class);
        $eventReadStore->reboot();

        $command = new SignUpCommand(
            Uuid::uuid4()->toString(),
            'email@domain.com',
            'password'
        );

        $this->handle($command);

        $uuid = Uuid::uuid4();

        /** @var SendEventsToElasticConsumer $consumer */
        $consumer = $this->service(SendEventsToElasticConsumer::class);
        $consumer(new Event(
            new DomainMessage(
                $uuid->toString(),
                1,
                new Metadata(),
                new UserWasCreated(
                    $uuid,
                    new Credentials(
                        Email::fromString('second_email@domain.com'),
                        HashedPassword::fromHash('hashed_password')
                    ),
                    DomainDateTime::now()
                ),
                DateTime::now()
            )
        ));

        $this->fireTerminateEvent();

        $eventReadStore->refresh();
    }

    /**
     * @test
     *
     * @group integration
     */
    public function processed_events_must_be_in_elasticsearch(): void
    {
        /** @var Paginator $response */
        $response = $this->ask(new GetEventsQuery([
            'operation_type' => 'collection',
            'collection_operation_name' => 'get',
            'resource_class' => DomainEvent::class,
        ]));

        $results = \iterator_to_array($response->getIterator());
        self::assertInstanceOf(Paginator::class, $response);
        self::assertSame(1, $response->count());
        self::assertInstanceOf(DomainEvent::class, $results[0]);
        self::assertSame('App.Domain.User.Event.UserWasCreated', $results[0]->type());
    }

    protected function tearDown(): void
    {
        /** @var DomainEventElasticsearchRepository $eventReadStore */
        $eventReadStore = $this->service(DomainEventElasticsearchRepository::class);
        $eventReadStore->delete();

        parent::tearDown();
    }
}
