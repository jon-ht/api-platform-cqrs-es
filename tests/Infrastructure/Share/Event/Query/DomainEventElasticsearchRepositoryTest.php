<?php

declare(strict_types=1);

namespace App\Tests\Infrastructure\Share\Event\Query;

use App\Domain\Shared\Exception\DateTimeException;
use App\Domain\Shared\ValueObject\DateTime as DomainDateTime;
use App\Domain\User\Event\UserWasCreated;
use App\Domain\User\ValueObject\Auth\Credentials;
use App\Domain\User\ValueObject\Auth\HashedPassword;
use App\Domain\User\ValueObject\Email;
use App\Infrastructure\Share\Event\Query\DomainEventElasticsearchRepository;
use App\Tests\Application\ApplicationTestCase;
use Broadway\Domain\DateTime;
use Broadway\Domain\DomainMessage;
use Broadway\Domain\Metadata;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class DomainEventElasticsearchRepositoryTest extends ApplicationTestCase
{
    private ?DomainEventElasticsearchRepository $repository;

    protected function setUp(): void
    {
        /** @var NormalizerInterface $normalizer */
        $normalizer = $this->service(NormalizerInterface::class);
        $this->repository = new DomainEventElasticsearchRepository(
            [
                'hosts' => [
                    'elasticsearch',
                ],
            ],
            $normalizer
        );
    }

    /**
     * @test
     *
     * @group integration
     *
     * @throws DateTimeException
     */
    public function an_event_should_be_stored_in_elastic(): void
    {
        $uuid = Uuid::uuid4();
        $event = new DomainMessage(
            $uuid->toString(),
            1,
            new Metadata(),
            new UserWasCreated(
                $uuid,
                new Credentials(
                    Email::fromString('email@domain.com'),
                    HashedPassword::fromHash('hashed_password')
                ),
                DomainDateTime::now()
            ),
            DateTime::now()
        );

        $this->repository->store($event);
        $this->repository->refresh();

        $result = $this->repository->search([
            'query' => [
                'match' => [
                    'type' => $event->getType(),
                ],
            ],
        ]);

        self::assertSame(1, $result['hits']['total']['value']);
    }

    protected function tearDown(): void
    {
        $this->repository->delete();
        $this->repository = null;
    }
}
