<?php

declare(strict_types=1);

namespace App\UI\Http\Rest\Controller;

use App\Infrastructure\Share\Bus\Query\QueryBus;
use App\Infrastructure\Share\Bus\Query\QueryInterface;

abstract class QueryController
{
    private QueryBus $queryBus;

    public function __construct(QueryBus $queryBus)
    {
        $this->queryBus = $queryBus;
    }

    /**
     * @return mixed
     *
     * @throws \Throwable
     */
    protected function ask(QueryInterface $query)
    {
        return $this->queryBus->handle($query);
    }
}
