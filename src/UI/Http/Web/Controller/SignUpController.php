<?php

declare(strict_types=1);

namespace App\UI\Http\Web\Controller;

use ApiPlatform\Core\Validator\Exception\ValidationException;
use ApiPlatform\Core\Validator\ValidatorInterface;
use App\Application\Command\User\SignUp\SignUpCommand;
use App\Application\Command\User\SignUp\SignUpInput;
use App\Infrastructure\User\Auth\Guard\LoginAuthenticator;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;

class SignUpController extends AbstractRenderController
{
    /**
     * @Route(
     *     "/sign-up",
     *     name="sign-up",
     *     methods={"GET"}
     * )
     */
    public function get(): Response
    {
        return $this->render('signup/index.html.twig');
    }

    /**
     * @Route(
     *     "/sign-up",
     *     name="sign-up-post",
     *     methods={"POST"}
     * )
     */
    public function post(
        Request $request,
        GuardAuthenticatorHandler $guardAuthenticatorHandler,
        Security $security,
        LoginAuthenticator $authenticator,
        ValidatorInterface $validator
    ): Response {
        $email = (string) $request->request->get('email');
        $password = (string) $request->request->get('password');
        $uuid = Uuid::uuid4()->toString();

        $input = new SignUpInput();
        $input->uuid = $uuid;
        $input->email = $email;
        $input->password = $password;

        try {
            $validator->validate($input);

            $this->exec(new SignUpCommand($input->uuid, $input->email, $input->password));

            return $guardAuthenticatorHandler->authenticateUserAndHandleSuccess(
                $security->getUser(),
                $request,
                $authenticator,
                'secured_area'
            );
        } catch (ValidationException $exception) {
            return $this->render('signup/index.html.twig', ['error' => $exception], Response::HTTP_BAD_REQUEST);
        } catch (\InvalidArgumentException $exception) {
            return $this->render('signup/index.html.twig', ['error' => $exception->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }
}
