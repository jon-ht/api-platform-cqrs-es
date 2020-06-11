<?php

declare(strict_types=1);

namespace App\Bridge\ApiPlatform\Swagger;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class HealthzSwaggerDecorator implements NormalizerInterface
{
    private NormalizerInterface $decorated;

    public function __construct(NormalizerInterface $decorated)
    {
        $this->decorated = $decorated;
    }

    public function supportsNormalization($data, string $format = null): bool
    {
        return $this->decorated->supportsNormalization($data, $format);
    }

    /**
     * {@inheritdoc}
     *
     * @return array|string|int|float|bool|\ArrayObject<string, mixed>|null
     */
    public function normalize($object, string $format = null, array $context = [])
    {
        /** @var array $docs */
        $docs = $this->decorated->normalize($object, $format, $context);

        $healthzDocumentation = [
            'paths' => [
                '/api/healthz' => [
                    'get' => [
                        'tags' => ['Healthz'],
                        'operationId' => 'getHealthz',
                        'summary' => 'API healthcheck',
                        'responses' => [
                            Response::HTTP_OK => [
                                'description' => 'Everything is OK',
                            ],
                            Response::HTTP_INTERNAL_SERVER_ERROR => [
                                'description' => 'Something went wrong',
                            ],
                        ],
                    ],
                ],
            ],
        ];

        return \array_merge_recursive($docs, $healthzDocumentation);
    }
}
