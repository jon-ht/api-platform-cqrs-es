<?php

declare(strict_types=1);

namespace App\Tests\Application\Query;

use ApiPlatform\Core\DataProvider\Pagination;
use ApiPlatform\Core\Metadata\Resource\Factory\ResourceMetadataFactoryInterface;
use ApiPlatform\Core\Metadata\Resource\ResourceMetadata;
use App\Application\Query\CollectionQueryFactory;
use PHPUnit\Framework\TestCase;

class CollectionQueryFactoryTest extends TestCase
{
    /**
     * @test
     *
     * @group unit
     */
    public function given_a_non_collection_query_class_it_should_throw_an_exception(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $resourceMetadataFactory = $this->createMock(ResourceMetadataFactoryInterface::class);

        $pagination = new Pagination($resourceMetadataFactory);

        $collectionQueryFactory = new CollectionQueryFactory([], $pagination);

        $collectionQueryFactory->createCollectionQuery('Foo', []);
    }

    /**
     * @test
     *
     * @group unit
     */
    public function given_an_empty_context_it_should_have_default_values(): void
    {
        $resourceMetadata = new ResourceMetadata();

        $resourceMetadataFactory = $this->createMock(ResourceMetadataFactoryInterface::class);
        $resourceMetadataFactory->method('create')
            ->willReturn($resourceMetadata);

        $pagination = new Pagination($resourceMetadataFactory);

        $collectionQueryFactory = new CollectionQueryFactory([], $pagination);

        $query = $collectionQueryFactory->createCollectionQuery(DummyCollectionQuery::class, []);

        self::assertSame([
            'operation_type' => 'collection',
            'collection_operation_name' => 'get',
            'resource_class' => 'Foo',
            'filters' => [
                'page' => 1,
                'itemsPerPage' => 30,
            ],
        ], $query->context());
    }

    /**
     * @test
     *
     * @group unit
     */
    public function given_collection_options_it_should_override_default_values(): void
    {
        $resourceMetadata = new ResourceMetadata();

        $resourceMetadataFactory = $this->createMock(ResourceMetadataFactoryInterface::class);
        $resourceMetadataFactory->method('create')
            ->willReturn($resourceMetadata);

        $pagination = new Pagination($resourceMetadataFactory);

        $collectionQueryFactory = new CollectionQueryFactory([
            'page_parameter_name' => 'offset',
            'items_per_page_parameter_name' => 'limit',
        ], $pagination);

        $query = $collectionQueryFactory->createCollectionQuery(DummyCollectionQuery::class, []);

        self::assertSame([
            'operation_type' => 'collection',
            'collection_operation_name' => 'get',
            'resource_class' => 'Foo',
            'filters' => [
                'offset' => 1,
                'limit' => 30,
            ],
        ], $query->context());
    }

    /**
     * @test
     *
     * @group unit
     */
    public function given_filters_in_context_it_should_merge_default_values(): void
    {
        $resourceMetadata = new ResourceMetadata();

        $resourceMetadataFactory = $this->createMock(ResourceMetadataFactoryInterface::class);
        $resourceMetadataFactory->method('create')
            ->willReturn($resourceMetadata);

        $pagination = new Pagination($resourceMetadataFactory);

        $collectionQueryFactory = new CollectionQueryFactory([], $pagination);

        $query = $collectionQueryFactory->createCollectionQuery(DummyCollectionQuery::class, [
            'filters' => ['field' => 'value'],
        ]);

        self::assertSame([
            'operation_type' => 'collection',
            'collection_operation_name' => 'get',
            'resource_class' => 'Foo',
            'filters' => [
                'field' => 'value',
                'page' => 1,
                'itemsPerPage' => 30,
            ],
        ], $query->context());
    }
}
