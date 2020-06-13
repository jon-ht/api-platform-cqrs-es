<?php

declare(strict_types=1);

namespace App\Infrastructure\Bridge\ApiPlatform\Elasticsearch\Filter;

use ApiPlatform\Core\Api\ResourceClassResolverInterface;
use ApiPlatform\Core\Bridge\Elasticsearch\DataProvider\Filter\AbstractFilter;
use ApiPlatform\Core\Bridge\Elasticsearch\DataProvider\Filter\ConstantScoreFilterInterface;
use ApiPlatform\Core\Metadata\Property\Factory\PropertyMetadataFactoryInterface;
use ApiPlatform\Core\Metadata\Property\Factory\PropertyNameCollectionFactoryInterface;
use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;
use Symfony\Component\Serializer\NameConverter\NameConverterInterface;

class RangeFilter extends AbstractFilter implements ConstantScoreFilterInterface
{
    private const RANGE_PARAMETERS = [
        'gt',
        'gte',
        'lt',
        'lte',
    ];

    public function __construct(
        PropertyNameCollectionFactoryInterface $propertyNameCollectionFactory,
        PropertyMetadataFactoryInterface $propertyMetadataFactory,
        ResourceClassResolverInterface $resourceClassResolver,
        ?NameConverterInterface $nameConverter = null,
        ?array $properties = null
    ) {
        parent::__construct(
            $propertyNameCollectionFactory,
            $propertyMetadataFactory,
            $resourceClassResolver,
            $nameConverter ?? new CamelCaseToSnakeCaseNameConverter(),
            $properties
        );
    }

    public function apply(array $clauseBody, string $resourceClass, ?string $operationName = null, array $context = []): array
    {
        $ranges = [];

        foreach ($context['filters'] ?? [] as $property => $values) {
            [$type] = $this->getMetadata($resourceClass, $property);

            if (!$type || !$values = (array) $values) {
                continue;
            }

            $property = null === $this->nameConverter ? $property : $this->nameConverter->normalize($property, $resourceClass, null, $context);

            if (null !== $nestedPath = $this->getNestedFieldPath($resourceClass, $property)) {
                $nestedPath = null === $this->nameConverter ? $nestedPath : $this->nameConverter->normalize($nestedPath, $resourceClass, null, $context);
            }

            $ranges[] = $this->getQuery($property, $values, $nestedPath);
        }

        if (!$ranges) {
            return $clauseBody;
        }

        return \array_merge_recursive($clauseBody, [
            'bool' => [
                'must' => $ranges,
            ],
        ]);
    }

    public function getDescription(string $resourceClass): array
    {
        $description = [];

        foreach ($this->getProperties($resourceClass) as $property) {
            [$type] = $this->getMetadata($resourceClass, $property);

            if (!$type) {
                continue;
            }

            foreach (self::RANGE_PARAMETERS as $parameter) {
                $description[\sprintf('%s[%s]', $property, $parameter)] = [
                    'property' => $property,
                    'type' => 'string',
                    'required' => false,
                ];
            }
        }

        return $description;
    }

    protected function getQuery(string $property, array $values, ?string $nestedPath): array
    {
        $ranges = [];

        foreach (self::RANGE_PARAMETERS as $parameter) {
            if (!isset($values[$parameter])) {
                continue;
            }

            $ranges[$parameter] = $values[$parameter];
        }

        return ['range' => [$nestedPath ?? $property => $ranges]];
    }
}
