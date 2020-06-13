<?php

declare(strict_types=1);

namespace App\Infrastructure\Bridge\ApiPlatform\DataProvider;

use ApiPlatform\Core\Bridge\Elasticsearch\DataProvider\Extension\RequestBodySearchCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Elasticsearch\DataProvider\Paginator;
use ApiPlatform\Core\DataProvider\ContextAwareCollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\Pagination;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use ApiPlatform\Core\Exception\ResourceClassNotFoundException;
use ApiPlatform\Core\Metadata\Resource\Factory\ResourceMetadataFactoryInterface;
use ApiPlatform\Core\Serializer\InputOutputMetadataTrait;
use Symfony\Component\DependencyInjection\ServiceLocator;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

/**
 * Similar to {@see \ApiPlatform\Core\Bridge\Elasticsearch\DataProvider\CollectionDataProvider}
 * It takes precedence over it to allow custom elasticsearch repository/client
 */
class ElasticsearchDataProvider implements ContextAwareCollectionDataProviderInterface, RestrictedDataProviderInterface
{
    use InputOutputMetadataTrait;

    private ServiceLocator $repositories;

    /** @var RequestBodySearchCollectionExtensionInterface[]|iterable */
    private $collectionExtensions;

    private Pagination $pagination;

    private DenormalizerInterface $denormalizer;

    public function __construct(
        ServiceLocator $repositories,
        iterable $collectionExtensions,
        Pagination $pagination,
        DenormalizerInterface $denormalizer,
        ResourceMetadataFactoryInterface $resourceMetadataFactory
    ) {
        $this->repositories = $repositories;
        $this->collectionExtensions = $collectionExtensions;
        $this->pagination = $pagination;
        $this->denormalizer = $denormalizer;
        $this->resourceMetadataFactory = $resourceMetadataFactory;
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        try {
            $this->resourceMetadataFactory->create($resourceClass);
        } catch (ResourceClassNotFoundException $e) {
            return false;
        }

        return $this->repositories->has($resourceClass);
    }

    public function getCollection(string $resourceClass, string $operationName = null, array $context = [])
    {
        $outputClass = $this->getOutputClass($resourceClass, $context) ?? $resourceClass;

        $body = [];

        foreach ($this->collectionExtensions as $collectionExtension) {
            $body = $collectionExtension->applyToCollection($body, $resourceClass, $operationName, $context);
        }

        $limit = $body['size'] = $body['size'] ?? $this->pagination->getLimit($resourceClass, $operationName, $context);
        $offset = $body['from'] = $body['from'] ?? $this->pagination->getOffset($resourceClass, $operationName, $context);

        $documents = $this->repositories->get($resourceClass)->search($body);

        return new Paginator(
            $this->denormalizer,
            $documents,
            $outputClass,
            $limit,
            $offset
        );
    }
}
