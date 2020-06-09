<?php

declare(strict_types=1);

namespace App\UI\Http\Rest\Controller\Healthz;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

final class HealthzController
{
    /**
     * API healthcheck
     *
     * @Route(
     *     "/healthz",
     *     name="healthz",
     *     methods={"GET"}
     * )
     */
    public function __invoke(): JsonResponse
    {
        return new JsonResponse();
    }
}
