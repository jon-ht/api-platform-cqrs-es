<?php

declare(strict_types=1);

namespace App\Tests\Application\Command\User\ChangeEmail;

use ApiPlatform\Core\Bridge\Symfony\Validator\Validator;
use App\Application\Command\User\ChangeEmail\ChangeEmailDataTransformer;
use App\Application\Command\User\ChangeEmail\ChangeEmailInput;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

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

        $validator = $this->createMock(Validator::class);

        $dataTransformer = new ChangeEmailDataTransformer($validator);

        $dataTransformer->transform(new \stdClass(), 'Foo', ['uuid' => Uuid::uuid4()]);
    }

    /**
     * @test
     *
     * @group unit
     */
    public function missing_uuid_in_context_should_throw_an_exception(): void
    {
        $this->expectException(\RuntimeException::class);

        $validator = $this->createMock(Validator::class);

        $dataTransformer = new ChangeEmailDataTransformer($validator);

        $dataTransformer->transform(new ChangeEmailInput(), 'Foo');
    }

    /**
     * @test
     *
     * @group unit
     */
    public function invalid_uuid_value_in_context_should_throw_an_exception(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $validator = $this->createMock(Validator::class);

        $dataTransformer = new ChangeEmailDataTransformer($validator);

        $dataTransformer->transform(new ChangeEmailInput(), 'Foo', ['uuid' => 'uuid']);
    }
}
