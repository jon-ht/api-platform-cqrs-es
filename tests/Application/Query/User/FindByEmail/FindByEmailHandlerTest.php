<?php

declare(strict_types=1);

namespace App\Tests\Application\Query\User\FindByEmail;

use App\Application\Command\User\SignUp\SignUpCommand;
use App\Application\Query\User\FindByEmail\FindByEmailQuery;
use App\Infrastructure\User\Query\Projections\UserView;
use App\Tests\Application\ApplicationTestCase;
use Ramsey\Uuid\Uuid;

class FindByEmailHandlerTest extends ApplicationTestCase
{
    /**
     * @test
     *
     * @group integration
     */
    public function query_command_integration(): void
    {
        $email = $this->createUserRead();

        $this->fireTerminateEvent();

        /** @var UserView $result */
        $result = $this->ask(new FindByEmailQuery($email));

        self::assertInstanceOf(UserView::class, $result);
        self::assertSame($email, $result->email());
    }

    private function createUserRead(): string
    {
        $uuid = Uuid::uuid4()->toString();
        $email = 'email@domain.com';

        $this->handle(new SignUpCommand($uuid, $email, 'password'));

        return $email;
    }
}
