<?php

declare(strict_types=1);

namespace App\Tests\UI\Http\Rest\Controller;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\Client;
use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\Response;
use App\Application\Command\User\SignUp\SignUpCommand;
use App\Infrastructure\Share\Bus\Command\CommandBus;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

abstract class JsonApiTestCase extends ApiTestCase
{
    public const DEFAULT_EMAIL = 'email@domain.com';

    public const DEFAULT_PASS = 'password';

    protected ?Client $cli;

    private ?string $token = null;

    protected ?UuidInterface $userUuid;

    protected function setUp(): void
    {
        self::ensureKernelShutdown();
        $this->cli = static::createClient();
    }

    protected function createUser(string $email = self::DEFAULT_EMAIL, string $password = self::DEFAULT_PASS): string
    {
        $this->userUuid = Uuid::uuid4();

        $signUp = new SignUpCommand(
            $this->userUuid->toString(),
            $email,
            $password
        );

        /** @var CommandBus $commandBus */
        $commandBus = $this->cli->getContainer()->get(CommandBus::class);

        $commandBus->handle($signUp);

        return $email;
    }

    /**
     * @return Response|ResponseInterface
     */
    protected function post(string $uri, array $params)
    {
        $options = \array_merge(
            $params,
            [
                'headers' => \array_merge($this->headers(), $params['headers'] ?? []),
            ]
        );

        if ($this->token) {
            $options['auth_bearer'] = $params['auth_bearer'] ?? $this->token;
        }

        return $this->cli->request(
            'POST',
            $uri,
            $options
        );
    }

    /**
     * @return Response|ResponseInterface
     */
    protected function put(string $uri, array $params)
    {
        $options = \array_merge(
            $params,
            [
                'headers' => \array_merge($this->headers(), $params['headers'] ?? []),
            ]
        );

        if ($this->token) {
            $options['auth_bearer'] = $params['auth_bearer'] ?? $this->token;
        }

        return $this->cli->request(
            'PUT',
            $uri,
            $options
        );
    }

    /**
     * @return Response|ResponseInterface
     */
    protected function get(string $uri, array $params = [])
    {
        $options = \array_merge(
            $params,
            [
                'headers' => \array_merge($this->headers(), $params['headers'] ?? []),
            ]
        );

        if ($this->token) {
            $options['auth_bearer'] = $params['auth_bearer'] ?? $this->token;
        }

        return $this->cli->request(
            'GET',
            $uri,
            $options
        );
    }

    protected function auth(string $username = self::DEFAULT_EMAIL, string $password = self::DEFAULT_PASS): void
    {
        $response = $this->post('/api/auth_check', [
            'json' => [
                'username' => $username ?: self::DEFAULT_EMAIL,
                'password' => $password ?: self::DEFAULT_PASS,
            ],
        ]);

        $this->token = $response->toArray()['token'];
    }

    protected function logout(): void
    {
        $this->token = null;
    }

    private function headers(): array
    {
        return [
            'content-type' => 'application/json',
        ];
    }

    protected function tearDown(): void
    {
        $this->cli = null;
        $this->token = null;
        $this->userUuid = null;
    }
}
