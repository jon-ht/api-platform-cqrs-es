<?php

declare(strict_types=1);

namespace App\Bridge\ApiPlatform\Metadata\Property\Factory;

use ApiPlatform\Core\Metadata\Property\Factory\PropertyMetadataFactoryInterface;
use ApiPlatform\Core\Metadata\Property\PropertyMetadata;
use App\Domain\Shared\ValueObject\AbstractString;
use Symfony\Component\PropertyInfo\Type;

class ValueObjectPropertyMetadataFactory implements PropertyMetadataFactoryInterface
{
    private PropertyMetadataFactoryInterface $decorated;

    public function __construct(PropertyMetadataFactoryInterface $decorated)
    {
        $this->decorated = $decorated;
    }

    public function create(string $resourceClass, string $property, array $options = []): PropertyMetadata
    {
        $propertyMetadata = $this->decorated->create($resourceClass, $property, $options);

        // Force value object to be represented as string instead og object
        if (($type = $propertyMetadata->getType()) && \is_a($type->getClassName(), AbstractString::class, true)) {
            $propertyMetadata = $propertyMetadata->withType(new Type(Type::BUILTIN_TYPE_STRING));
        }

        return $propertyMetadata;
    }
}
