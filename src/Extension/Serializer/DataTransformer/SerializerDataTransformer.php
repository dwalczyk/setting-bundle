<?php

declare(strict_types=1);

namespace DWalczyk\SettingBundle\Extension\Serializer\DataTransformer;

use DWalczyk\SettingBundle\AbstractDataTransformer;
use DWalczyk\SettingBundle\Exception\DataTransformer\ReverseTransformException;
use DWalczyk\SettingBundle\SettingDefinition;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;
use Symfony\Component\Serializer\SerializerInterface;

final class SerializerDataTransformer extends AbstractDataTransformer
{
    public function __construct(
        private readonly SerializerInterface $serializer,
        private readonly string $serializerFormat = 'json'
    ) {
    }

    public function transform(mixed $value, SettingDefinition $item): ?string
    {
        if (null === $value) {
            return null;
        }

        try {
            return $this->serializer->serialize($value, $this->serializerFormat);
        } catch (NotNormalizableValueException $e) {
            throw new ReverseTransformException($value, $item, $e);
        }
    }

    public function reverseTransform(mixed $value, SettingDefinition $item): mixed
    {
        if (null === $value) {
            return null;
        }

        $type = match ($item->type) {
            'boolean' => 'bool',
            'integer' => 'int',
            default => $item->type
        };

        try {
            return $this->serializer->deserialize($value, $type, $this->serializerFormat);
        } catch (NotNormalizableValueException $e) {
            throw new ReverseTransformException($value, $item, $e);
        }
    }
}
