<?php

declare(strict_types=1);

namespace App\Tests\Application\Command\User\SignUp;

use ApiPlatform\Core\Bridge\Symfony\Validator\Validator;
use App\Application\Command\User\SignUp\SignUpDataTransformer;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\RequestStack;

class SignUpDataTransformerTest extends TestCase
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

        $dataTransformer = new SignUpDataTransformer($requestStack, $validator);

        $dataTransformer->transform(new \stdClass(), 'Foo');
    }
}
