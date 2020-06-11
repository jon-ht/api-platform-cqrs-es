<?php

declare(strict_types=1);

namespace App\Application\Query\User\FindByEmail;

use App\Domain\Shared\Query\Exception\NotFoundException;
use App\Infrastructure\Share\Bus\Query\QueryHandlerInterface;
use App\Infrastructure\User\Query\Mysql\MysqlUserReadModelRepository;
use App\Infrastructure\User\Query\Projections\UserView;
use Doctrine\ORM\NonUniqueResultException;

class FindByEmailHandler implements QueryHandlerInterface
{
    private MysqlUserReadModelRepository $repository;

    public function __construct(MysqlUserReadModelRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @throws NotFoundException
     * @throws NonUniqueResultException
     */
    public function __invoke(FindByEmailQuery $query): UserView
    {
        return $this->repository->oneByEmail($query->email());
    }
}
