<?php

declare(strict_types=1);

namespace App\Tests\UI\Http\Rest\Controller\Auth;

use App\Domain\User\Event\UserSignedIn;
use App\Tests\Infrastructure\Share\Event\EventCollectorListener;
use App\Tests\UI\Http\Rest\Controller\JsonApiTestCase;
use Symfony\Component\HttpFoundation\Response;

class SignInEndpointTest extends JsonApiTestCase
{
    /**
     * @test
     *
     * @group e2e
     */
    public function empty_credentials_should_get_400_status_code(): void
    {
        $this->post('/api/auth_check', ['json' => []])->toArray(false);

        self::assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
    }

    /**
     * @test
     *
     * @group e2e
     */
    public function invalid_user_email_should_get_401_status_code(): void
    {
        $this->createUser();

        $response = $this->post('/api/auth_check', ['json' => [
            'username' => 'wrong_email@domain.com',
            'password' => self::DEFAULT_PASS,
        ]])->toArray(false);

        self::assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);

        self::assertArrayHasKey('code', $response);
        self::assertArrayHasKey('message', $response);
        self::assertSame(401, $response['code']);
        self::assertSame('Invalid credentials.', $response['message']);
    }

    /**
     * @test
     *
     * @group e2e
     */
    public function invalid_user_password_should_get_401_status_code(): void
    {
        $this->createUser();

        $response = $this->post('/api/auth_check', ['json' => [
            'username' => self::DEFAULT_EMAIL,
            'password' => 'wrong password',
        ]])->toArray(false);

        self::assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);

        self::assertArrayHasKey('code', $response);
        self::assertArrayHasKey('message', $response);
        self::assertSame(401, $response['code']);
        self::assertSame('Invalid credentials.', $response['message']);
    }

    /**
     * @test
     *
     * @group e2e
     */
    public function valid_credentials_should_return_token(): void
    {
        $this->createUser();

        $response = $this->post('/api/auth_check', ['json' => [
            'username' => self::DEFAULT_EMAIL,
            'password' => self::DEFAULT_PASS,
        ]])->toArray(false);

        self::assertResponseStatusCodeSame(Response::HTTP_OK);

        self::assertArrayHasKey('token', $response);

        /** @var EventCollectorListener $eventCollector */
        $eventCollector = $this->cli->getContainer()->get(EventCollectorListener::class);

        $events = $eventCollector->popEvents();

        self::assertCount(2, $events);

        $payload = $events[1]->getPayload();

        self::assertInstanceOf(UserSignedIn::class, $payload);
    }
}
