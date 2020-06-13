<?php

declare(strict_types=1);

namespace App\Tests\Infrastructure\Share\Serializer;

use App\Infrastructure\Share\Serializer\BroadwaySerializer;
use Assert\AssertionFailedException;
use Broadway\Serializer\SerializationException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class BroadwaySerializerTest extends TestCase
{
    /** @var \PHPUnit\Framework\MockObject\MockObject|NormalizerInterface */
    private $normalizer;

    /** @var \PHPUnit\Framework\MockObject\MockObject|DenormalizerInterface */
    private $denormalizer;

    protected function setUp(): void
    {
        $this->normalizer = $this->createMock(NormalizerInterface::class);
        $this->denormalizer = $this->createMock(DenormalizerInterface::class);
    }

    /**
     * @test
     *
     * @group unit
     */
    public function serializing_non_supported_object_should_throw_an_exception(): void
    {
        $this->expectException(SerializationException::class);

        $this->normalizer
            ->method('supportsNormalization')
            ->willReturn(false)
        ;

        $serializer = new BroadwaySerializer($this->normalizer, $this->denormalizer);
        $serializer->serialize(new \stdClass());
    }

    /**
     * @test
     *
     * @group unit
     */
    public function deserializing_to_non_supported_object_should_throw_an_exception(): void
    {
        $this->expectException(SerializationException::class);

        $this->denormalizer
            ->method('supportsDenormalization')
            ->willReturn(false)
        ;

        $serializer = new BroadwaySerializer($this->normalizer, $this->denormalizer);
        $serializer->deserialize([
            'class' => \stdClass::class,
            'payload' => [],
        ]);
    }

    /**
     * @test
     *
     * @group unit
     */
    public function invalid_serialized_object_should_throw_an_exception(): void
    {
        $this->expectException(AssertionFailedException::class);

        $serializer = new BroadwaySerializer($this->normalizer, $this->denormalizer);
        $serializer->deserialize([]);
    }
}
