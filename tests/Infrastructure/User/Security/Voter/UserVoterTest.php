<?php

declare(strict_types=1);

namespace App\Tests\Infrastructure\User\Security\Voter;

use App\Infrastructure\User\Auth\Auth;
use App\Infrastructure\User\Security\Voter\UserVoter;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use stdClass;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class UserVoterTest extends TestCase
{
    /** @var \PHPUnit\Framework\MockObject\MockObject|TokenInterface */
    private $token;

    protected function setUp(): void
    {
        parent::setUp();

        $this->token = $this->createMock(TokenInterface::class);
    }

    /**
     * @test
     *
     * @group unit
     */
    public function given_an_invalid_attribute_it_should_not_grant_access(): void
    {
        $voter = new UserVoter();

        self::assertNotSame(VoterInterface::ACCESS_GRANTED, $voter->vote($this->token, 'uuid', ['invalid_attribute']));
    }

    /**
     * @test
     *
     * @group unit
     */
    public function given_an_invalid_subject_it_should_not_grant_access(): void
    {
        $voter = new UserVoter();

        self::assertNotSame(VoterInterface::ACCESS_GRANTED, $voter->vote($this->token, new stdClass(), ['user_change_email']));
    }

    /**
     * @test
     *
     * @group unit
     */
    public function given_an_invalid_uuid_string_subject_it_should_not_grant_access(): void
    {
        $voter = new UserVoter();

        self::assertNotSame(VoterInterface::ACCESS_GRANTED, $voter->vote($this->token, 'uuid', ['user_change_email']));
    }

    /**
     * @test
     *
     * @group unit
     */
    public function given_a_valid_uuid_string_subject_it_should_not_grant_access_if_not_logged_in(): void
    {
        $this->token->method('getUser')
            ->willReturn(null);

        $uuid = Uuid::uuid4();

        $voter = new UserVoter();

        self::assertNotSame(
            VoterInterface::ACCESS_GRANTED,
            $voter->vote($this->token, $uuid->toString(), ['user_change_email'])
        );
    }

    /**
     * @test
     *
     * @group unit
     */
    public function given_a_valid_uuid_object_subject_it_should_not_grant_access_if_not_logged_in(): void
    {
        $this->token->method('getUser')
            ->willReturn(null);

        $uuid = Uuid::uuid4();

        $voter = new UserVoter();

        self::assertNotSame(
            VoterInterface::ACCESS_GRANTED,
            $voter->vote($this->token, $uuid, ['user_change_email'])
        );
    }

    /**
     * @test
     *
     * @group unit
     */
    public function given_a_valid_uuid_string_subject_it_should_not_grant_access_if_not_same_as_auth_uuid(): void
    {
        $auth = $this->createMock(Auth::class);
        $auth->method('uuid')
            ->willReturn(Uuid::uuid4());

        $this->token->method('getUser')
            ->willReturn($auth);

        $uuid = Uuid::uuid4();

        $voter = new UserVoter();

        self::assertNotSame(
            VoterInterface::ACCESS_GRANTED,
            $voter->vote($this->token, $uuid->toString(), ['user_change_email'])
        );
    }

    /**
     * @test
     *
     * @group unit
     */
    public function given_a_valid_uuid_subject_it_should_not_grant_access_if_not_same_as_auth_uuid(): void
    {
        $auth = $this->createMock(Auth::class);
        $auth->method('uuid')
            ->willReturn(Uuid::uuid4());

        $this->token->method('getUser')
            ->willReturn($auth);

        $uuid = Uuid::uuid4();

        $voter = new UserVoter();

        self::assertNotSame(
            VoterInterface::ACCESS_GRANTED,
            $voter->vote($this->token, $uuid, ['user_change_email'])
        );
    }

    /**
     * @test
     *
     * @group unit
     */
    public function given_a_valid_uuid_string_subject_it_should_grant_access_if_same_as_auth_uuid(): void
    {
        $uuid = Uuid::uuid4();
        $auth = $this->createMock(Auth::class);
        $auth->method('uuid')
            ->willReturn($uuid);

        $this->token->method('getUser')
            ->willReturn($auth);

        $voter = new UserVoter();

        self::assertSame(
            VoterInterface::ACCESS_GRANTED,
            $voter->vote($this->token, $uuid->toString(), ['user_change_email'])
        );
    }

    /**
     * @test
     *
     * @group unit
     */
    public function given_a_valid_uuid_subject_it_should_grant_access_if_same_as_auth_uuid(): void
    {
        $uuid = Uuid::uuid4();
        $auth = $this->createMock(Auth::class);
        $auth->method('uuid')
            ->willReturn($uuid);

        $this->token->method('getUser')
            ->willReturn($auth);

        $voter = new UserVoter();

        self::assertSame(
            VoterInterface::ACCESS_GRANTED,
            $voter->vote($this->token, $uuid, ['user_change_email'])
        );
    }
}
