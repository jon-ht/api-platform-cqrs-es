<?php

declare(strict_types=1);

namespace App\UI\Http\Web\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(
 *     "/profile",
 *     name="profile",
 *     methods={"GET"}
 * )
 */
class ProfileController extends AbstractRenderController
{
    public function __invoke(): Response
    {
        return $this->render('profile/index.html.twig');
    }
}
