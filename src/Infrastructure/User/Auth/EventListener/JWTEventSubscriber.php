<?php

declare(strict_types=1);

namespace App\Infrastructure\User\Auth\EventListener;

use App\Application\Command\User\SignIn\SignInCommand;
use App\Infrastructure\Share\Bus\Command\CommandBus;
use App\Infrastructure\User\Auth\Auth;
use JsonException;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Events;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class JWTEventSubscriber implements EventSubscriberInterface
{
    private CommandBus $commandBus;

    private LoggerInterface $logger;

    private RequestStack $requestStack;

    public function __construct(
        CommandBus $commandBus,
        LoggerInterface $logger,
        RequestStack $requestStack
    ) {
        $this->commandBus = $commandBus;
        $this->logger = $logger;
        $this->requestStack = $requestStack;
    }

    public static function getSubscribedEvents()
    {
        return [
            Events::JWT_CREATED => 'onJWTCreated',
        ];
    }

    public function onJWTCreated(JWTCreatedEvent $event): void
    {
        $user = $event->getUser();

        if ($user instanceof Auth && $request = $this->requestStack->getMasterRequest()) {
            try {
                $this->commandBus->handle(new SignInCommand(
                    $user->getUsername(),
                    \json_decode(
                        (string) $request->getContent(),
                        true,
                        512,
                        \JSON_THROW_ON_ERROR
                    )['password']
                ));
            } catch (JsonException $e) {
                $this->logger->error($e->getMessage());
            } catch (\Throwable $e) {
                // TODO maybe throw an authentication exception ?
                $this->logger->error($e->getMessage(), [
                    'command' => SignInCommand::class,
                    'payload' => [
                        'email' => $user->getUsername(),
                    ],
                ]);
            }
        }
    }
}
