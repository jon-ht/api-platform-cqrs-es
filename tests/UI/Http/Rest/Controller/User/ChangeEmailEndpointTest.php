<?php

declare(strict_types=1);

namespace App\Tests\UI\Http\Rest\Controller\User;

use App\Tests\Infrastructure\Share\Event\EventCollectorListener;
use App\Tests\UI\Http\Rest\Controller\JsonApiTestCase;
use Symfony\Component\HttpFoundation\Response;

class ChangeEmailEndpointTest extends JsonApiTestCase
{
    /**
     * @test
     *
     * @group e2e
     */
    public function not_logged_users_should_get_401_status_code(): void
    {
        $this->put('/api/users/uuid/email', []);

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
    public function user_cannot_change_other_user_email_and_should_get_401_status_code(): void
    {
        $this->createUser();
        $userUuid = $this->userUuid;

        $this->createUser('second_email@domain.com');
        $this->auth('second_email@domain.com', self::DEFAULT_PASS);

        $this->put(\sprintf('/api/users/%s/email', $userUuid), []);

        self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);

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

        $response = $this->put(\sprintf('/api/users/%s/email', $this->userUuid->toString()), ['json' => [
            'email' => 'email',
        ]])->toArray(false);

        self::assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);

        self::assertSame('ConstraintViolationList', $response['@type']);
        self::assertSame([
            ['propertyPath' => 'email', 'message' => 'This value is not a valid email address.'],
        ], $response['violations']);

        /** @var EventCollectorListener $eventCollector */
        $eventCollector = $this->cli->getContainer()->get(EventCollectorListener::class);

        $events = $eventCollector->popEvents();

        self::assertCount(0, $events);
    }
}
