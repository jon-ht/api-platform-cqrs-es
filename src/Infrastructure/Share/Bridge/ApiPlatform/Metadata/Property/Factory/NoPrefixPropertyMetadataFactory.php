<?php

declare(strict_types=1);

namespace App\Infrastructure\Share\Bridge\ApiPlatform\Metadata\Property\Factory;

use ApiPlatform\Core\Metadata\Property\Factory\PropertyMetadataFactoryInterface;
use ApiPlatform\Core\Metadata\Property\PropertyMetadata;
use Symfony\Component\PropertyInfo\PropertyInfoExtractorInterface;

class NoPrefixPropertyMetadataFactory implements PropertyMetadataFactoryInterface
{
    private PropertyInfoExtractorInterface $propertyInfo;

    private PropertyMetadataFactoryInterface $decorated;

    public function __construct(
        PropertyInfoExtractorInterface $propertyInfo,
        PropertyMetadataFactoryInterface $decorated
    ) {
        $this->propertyInfo = $propertyInfo;
        $this->decorated = $decorated;
    }

    /**
     * {@inheritdoc}
     */
    public function create(string $resourceClass, string $property, array $options = []): PropertyMetadata
    {
        $propertyMetadata = $this->decorated->create($resourceClass, $property, $options);

        /** @see \Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor::getReadInfo */
        $context = \array_merge([
            'enable_getter_setter_extraction' => true,
        ], $options);

        if (false === $propertyMetadata->isReadable() && null !== $readable = $this->propertyInfo->isReadable($resourceClass, $property, $context)) {
            $propertyMetadata = $propertyMetadata->withReadable($readable);
        }

        if (false === $propertyMetadata->isWritable() && null !== $writable = $this->propertyInfo->isWritable($resourceClass, $property, $context)) {
            $propertyMetadata = $propertyMetadata->withWritable($writable);
        }

        return $propertyMetadata;
    }
}
