<?php

declare(strict_types=1);

namespace App\Tests\UI\Http\Rest\Controller\User;

use App\Domain\User\Event\UserWasCreated;
use App\Tests\Infrastructure\Share\Event\EventCollectorListener;
use App\Tests\UI\Http\Rest\Controller\JsonApiTestCase;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\Response;

class SignUpEndpointTest extends JsonApiTestCase
{
    /**
     * @test
     *
     * @group e2e
     */
    public function empty_input_parameters_should_return_400_status_code(): void
    {
        $response = $this->post('/api/signup', ['json' => []])->toArray(false);

        self::assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);

        self::assertSame('ConstraintViolationList', $response['@type']);
        self::assertSame([
            ['propertyPath' => 'uuid', 'message' => 'This value should not be blank.'],
            ['propertyPath' => 'email', 'message' => 'This value should not be blank.'],
            ['propertyPath' => 'password', 'message' => 'This value should not be blank.'],
        ], $response['violations']);
    }

    /**
     * @test
     *
     * @group e2e
     */
    public function invalid_input_parameters_should_return_400_status_code(): void
    {
        $response = $this->post('/api/signup', ['json' => [
            'uuid' => 'uuid',
            'email' => 'email',
            'password' => 'pass',
        ]])->toArray(false);

        self::assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);

        self::assertSame('ConstraintViolationList', $response['@type']);
        self::assertSame([
            ['propertyPath' => 'uuid', 'message' => 'This is not a valid UUID.'],
            ['propertyPath' => 'email', 'message' => 'This value is not a valid email address.'],
            ['propertyPath' => 'password', 'message' => 'Min 6 characters password'],
        ], $response['violations']);
    }

    /**
     * @test
     *
     * @group e2e
     */
    public function existing_uuid_should_return_400_status_code(): void
    {
        $this->createUser();

        $response = $this->post('/api/signup', ['json' => [
            'uuid' => $this->userUuid->toString(),
            'email' => 'second_email@domain.com',
            'password' => self::DEFAULT_PASS,
        ]])->toArray(false);

        self::assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);

        self::assertSame('hydra:Error', $response['@type']);
        self::assertSame('UUID already registered.', $response['hydra:description']);
    }

    /**
     * @test
     *
     * @group e2e
     */
    public function existing_email_should_return_400_status_code(): void
    {
        $this->createUser();
        $this->auth();

        $response = $this->post('/api/signup', ['json' => [
            'uuid' => Uuid::uuid4()->toString(),
            'email' => self::DEFAULT_EMAIL,
            'password' => self::DEFAULT_PASS,
        ]])->toArray(false);

        self::assertResponseStatusCodeSame(Response::HTTP_CONFLICT);

        self::assertSame('hydra:Error', $response['@type']);
        self::assertSame('Email already registered.', $response['hydra:description']);
    }

    /**
     * @test
     *
     * @group e2e
     */
    public function valid_input_parameters_should_create_user_account(): void
    {
        $this->createUser();
        $this->auth();

        $this->post('/api/signup', ['json' => [
            'uuid' => Uuid::uuid4()->toString(),
            'email' => 'second_email@domain.com',
            'password' => self::DEFAULT_PASS,
        ]]);

        self::assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);

        /** @var EventCollectorListener $eventCollector */
        $eventCollector = $this->cli->getContainer()->get(EventCollectorListener::class);

        $events = $eventCollector->popEvents();

        self::assertCount(1, $events);

        $payload = $events[0]->getPayload();

        self::assertInstanceOf(UserWasCreated::class, $payload);
    }
}
