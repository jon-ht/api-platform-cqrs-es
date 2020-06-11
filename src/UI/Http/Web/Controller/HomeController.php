<?php

declare(strict_types=1);

namespace App\UI\Http\Web\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(
 *     "/",
 *     name="home",
 *     methods={"GET"}
 * )
 */
class HomeController extends AbstractRenderController
{
    public function __invoke(): Response
    {
        return $this->render('home/index.html.twig');
    }
}
