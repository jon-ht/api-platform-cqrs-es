<?php

declare(strict_types=1);

namespace App\Application\Command\User\SignIn;

use App\Domain\User\Exception\InvalidCredentialsException;
use App\Domain\User\Repository\GetUserUuidByEmailInterface;
use App\Domain\User\Repository\UserRepositoryInterface;
use App\Domain\User\ValueObject\Email;
use App\Infrastructure\Share\Bus\Command\CommandHandlerInterface;
use Ramsey\Uuid\UuidInterface;

class SignInHandler implements CommandHandlerInterface
{
    private UserRepositoryInterface $userStore;

    private GetUserUuidByEmailInterface $userUuidRepository;

    public function __construct(UserRepositoryInterface $userStore, GetUserUuidByEmailInterface $userUuidRepository)
    {
        $this->userStore = $userStore;
        $this->userUuidRepository = $userUuidRepository;
    }

    public function __invoke(SignInCommand $command): void
    {
        $uuid = $this->uuidFromEmail($command->email());

        $user = $this->userStore->get($uuid);

        $user->signIn($command->plainPassword());

        $this->userStore->store($user);
    }

    private function uuidFromEmail(Email $email): UuidInterface
    {
        $uuid = $this->userUuidRepository->getUuidByEmail($email);

        if (null === $uuid) {
            throw new InvalidCredentialsException();
        }

        return $uuid;
    }
}
