<?php

declare(strict_types=1);

namespace App\Infrastructure\Share\Specification;

use App\Domain\Shared\Exception\NonUniqueUuidException;
use App\Domain\Shared\Specification\UniqueAggregateRootSpecificationInterface;
use Doctrine\DBAL\Connection;
use Ramsey\Uuid\UuidInterface;

class UniqueAggregateRootSpecification implements UniqueAggregateRootSpecificationInterface
{
    private Connection $connection;

    private string $tableName;

    public function __construct(
        Connection $connection,
        string $tableName
    ) {
        $this->connection = $connection;
        $this->tableName = $tableName;
    }

    public function isUnique(UuidInterface $uuid): bool
    {
        $query = \sprintf('SELECT COUNT(1) FROM %s WHERE uuid = ?', $this->tableName);

        $statement = $this->connection->prepare($query);
        $statement->bindValue(1, $uuid->getBytes());
        $statement->execute();

        if ((int) $statement->fetchColumn(0) > 0) {
            throw new NonUniqueUuidException();
        }

        return true;
    }
}
