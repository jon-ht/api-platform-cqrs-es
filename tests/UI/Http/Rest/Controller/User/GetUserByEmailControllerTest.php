<?php

declare(strict_types=1);

namespace App\Tests\UI\Http\Rest\Controller\User;

use App\Tests\Infrastructure\Share\Event\EventCollectorListener;
use App\Tests\UI\Http\Rest\Controller\JsonApiTestCase;
use Symfony\Component\HttpFoundation\Response;

class GetUserByEmailControllerTest extends JsonApiTestCase
{
    /**
     * @test
     *
     * @group e2e
     */
    public function not_logged_users_should_get_401_status_code(): void
    {
        $this->get('/api/users/foo');

        self::assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);

        /** @var EventCollectorListener $eventCollector */
        $eventCollector = $this->cli->getContainer()->get(EventCollectorListener::class);

        $events = $eventCollector->popEvents();

        self::assertCount(0, $events);
    }

    /**
     * @test
     *
     * @group e2e
     */
    public function invalid_input_parameters_should_return_400_status_code(): void
    {
        $this->createUser();
        $this->auth();

        $this->get('/api/users/foo');

        self::assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);

        /** @var EventCollectorListener $eventCollector */
        $eventCollector = $this->cli->getContainer()->get(EventCollectorListener::class);

        $events = $eventCollector->popEvents();

        self::assertCount(0, $events);
    }

    /**
     * @test
     *
     * @group e2e
     */
    public function valid_input_parameters_should_return_404_status_code_when_not_exist(): void
    {
        $this->createUser();
        $this->auth();

        $this->get('/api/users/not_registered@domain.com');

        self::assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);

        /** @var EventCollectorListener $eventCollector */
        $eventCollector = $this->cli->getContainer()->get(EventCollectorListener::class);

        $events = $eventCollector->popEvents();

        self::assertCount(0, $events);
    }

    /**
     * @test
     *
     * @group e2e
     */
    public function valid_input_parameters_should_return_200_status_code_when_exist(): void
    {
        $emailString = $this->createUser();
        $this->auth();

        $this->get(\sprintf('/api/users/%s', $emailString));

        self::assertResponseIsSuccessful();

        /** @var EventCollectorListener $eventCollector */
        $eventCollector = $this->cli->getContainer()->get(EventCollectorListener::class);

        $events = $eventCollector->popEvents();

        self::assertCount(0, $events);
    }
}
