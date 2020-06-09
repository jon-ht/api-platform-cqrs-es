<?php

declare(strict_types=1);

namespace App\Infrastructure\User\Auth\EventListener;

use App\Application\Command\User\SignIn\SignInCommand;
use App\Infrastructure\Share\Bus\Command\CommandBus;
use App\Infrastructure\User\Auth\Auth;
use JsonException;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\SecurityEvents;
use Throwable;

class SecurityEventSubscriber implements EventSubscriberInterface
{
    private CommandBus $commandBus;

    private LoggerInterface $logger;

    public function __construct(CommandBus $commandBus, LoggerInterface $logger)
    {
        $this->commandBus = $commandBus;
        $this->logger = $logger;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            SecurityEvents::INTERACTIVE_LOGIN => 'onInteractiveLogin',
        ];
    }

    public function onInteractiveLogin(InteractiveLoginEvent $event): void
    {
        $user = $event->getAuthenticationToken()->getUser();

        if ($user instanceof Auth) {
            try {
                $this->commandBus->handle(new SignInCommand(
                    $user->getUsername(),
                    \json_decode(
                        (string) $event->getRequest()->getContent(),
                        true,
                        512,
                        \JSON_THROW_ON_ERROR
                    )['password']
                ));
            } catch (JsonException $e) {
                $this->logger->error($e->getMessage());
            } catch (Throwable $e) {
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
