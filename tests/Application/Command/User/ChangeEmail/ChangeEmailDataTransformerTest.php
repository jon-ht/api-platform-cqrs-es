<?php

declare(strict_types=1);

namespace App\Tests\Application\Command\User\ChangeEmail;

use ApiPlatform\Core\Bridge\Symfony\Validator\Validator;
use App\Application\Command\User\ChangeEmail\ChangeEmailDataTransformer;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\RequestStack;

class ChangeEmailDataTransformerTest extends TestCase
{
    /**
     * @test
     *
     * @group unit
     */
    public function given_an_invalid_input_it_should_throw_an_exception(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $requestStack = $this->createMock(RequestStack::class);
        $validator = $this->createMock(Validator::class);

        $dataTransformer = new ChangeEmailDataTransformer($requestStack, $validator);

        $dataTransformer->transform(new \stdClass(), 'Foo');
    }
}
