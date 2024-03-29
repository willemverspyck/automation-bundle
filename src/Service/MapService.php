<?php

declare(strict_types=1);

namespace Spyck\AutomationBundle\Service;

use Spyck\AutomationBundle\Parameter\ParameterInterface;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

readonly class MapService
{
    public function getMap(array $data, ParameterInterface $parameter): ParameterInterface
    {
        $serializer = new Serializer([
            new DateTimeNormalizer(),
            new ObjectNormalizer(propertyTypeExtractor: new ReflectionExtractor()),
        ]);

        $context = [
            AbstractObjectNormalizer::DISABLE_TYPE_ENFORCEMENT => true,
        ];

        return $serializer->denormalize($data, get_class($parameter), null, $context);
    }
}
