<?php

declare(strict_types=1);

namespace App\Infrastructure\User\Auth;

use ApiPlatform\Core\Bridge\Symfony\Validator\Exception\ValidationException;
use App\Domain\Shared\Query\Exception\NotFoundException;
use App\Domain\User\ValueObject\Email;
use App\Infrastructure\User\Query\Mysql\MysqlUserReadModelRepository;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class AuthProvider implements UserProviderInterface
{
    private MysqlUserReadModelRepository $userReadModelRepository;

    public function __construct(MysqlUserReadModelRepository $userReadModelRepository)
    {
        $this->userReadModelRepository = $userReadModelRepository;
    }

    /**
     * @throws \Doctrine\ORM\NonUniqueResultException
     *
     * @return Auth|UserInterface
     */
    public function loadUserByUsername(string $email)
    {
        try {
            [$uuid, $email, $hashedPassword] = $this->userReadModelRepository->getCredentialsByEmail(
                Email::fromString($email)
            );
        } catch (ValidationException | NotFoundException $exception) {
            throw new UsernameNotFoundException(\sprintf('User "%s" not found.', $email), 0, $exception);
        }

        return Auth::create($uuid, $email, $hashedPassword);
    }

    /**
     * @throws \App\Domain\Shared\Query\Exception\NotFoundException
     * @throws \Assert\AssertionFailedException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function refreshUser(UserInterface $user): UserInterface
    {
        return $this->loadUserByUsername($user->getUsername());
    }

    public function supportsClass(string $class): bool
    {
        return Auth::class === $class;
    }
}
