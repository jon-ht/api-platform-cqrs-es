<?php

declare(strict_types=1);

namespace App\Application\Command\User\SignUp;

use App\Domain\User\Factory\UserFactory;
use App\Domain\User\Repository\UserRepositoryInterface;
use App\Infrastructure\Share\Bus\Command\CommandHandlerInterface;

class SignUpHandler implements CommandHandlerInterface
{
    private UserRepositoryInterface $userRepository;

    private UserFactory $factory;

    public function __construct(
        UserRepositoryInterface $userRepository,
        UserFactory $factory
    ) {
        $this->userRepository = $userRepository;
        $this->factory = $factory;
    }

    /**
     * @throws \App\Domain\Shared\Exception\DateTimeException
     */
    public function __invoke(SignUpCommand $data): void
    {
        $user = $this->factory->create($data->uuid(), $data->credentials());

        $this->userRepository->store($user);
    }
}
