<?php

declare(strict_types=1);

namespace App\Application\Command\User\SignUp;

use App\Domain\Shared\Exception\DateTimeException;
use App\Domain\Shared\Exception\NonUniqueUuidException;
use App\Domain\User\Exception\EmailAlreadyExistException;
use App\Domain\User\Repository\UserRepositoryInterface;
use App\Domain\User\Specification\UniqueUserSpecificationInterface;
use App\Domain\User\User;
use App\Infrastructure\Share\Bus\Command\CommandHandlerInterface;

class SignUpHandler implements CommandHandlerInterface
{
    private UserRepositoryInterface $userRepository;

    private UniqueUserSpecificationInterface $uniqueUserSpecification;

    public function __construct(
        UserRepositoryInterface $userRepository,
        UniqueUserSpecificationInterface $uniqueUserSpecification
    ) {
        $this->userRepository = $userRepository;
        $this->uniqueUserSpecification = $uniqueUserSpecification;
    }

    /**
     * @throws DateTimeException
     * @throws NonUniqueUuidException
     * @throws EmailAlreadyExistException
     */
    public function __invoke(SignUpCommand $data): void
    {
        $user = User::create($data->uuid(), $data->credentials(), $this->uniqueUserSpecification);

        $this->userRepository->store($user);
    }
}
