<?php

declare(strict_types=1);

namespace DWalczyk\SettingBundle\Extension\Doctrine\DataTransformer;

use Doctrine\ORM\EntityManagerInterface;
use DWalczyk\SettingBundle\AbstractDataTransformer;
use DWalczyk\SettingBundle\SettingDefinition;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Serializer\SerializerInterface;

final class DoctrineDataTransformer extends AbstractDataTransformer
{
    public const REGEX_PATTERN = '/doctrine-entity<([a-zA-Z\\\\]+)>/';

    private PropertyAccessorInterface $propertyAccessor;

    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly SerializerInterface $serializer
    ) {
    }

    /**
     * @param object|array<object>|null $value
     */
    public function transform(mixed $value, SettingDefinition $item): ?string
    {
        if (null === $value) {
            return null;
        }

        if ($this->isMultiple($item)) {
            if (!\is_array($value)) {
                $value = [$value];
            }

            $valueToStorage = [];
            foreach ($value as $entity) {
                $valueToStorage[] = $this->resolveEntityId($entity, $item);
            }
        } else {
            if (\is_array($value)) {
                $entity = $value[\array_key_first($value)];
            } else {
                $entity = $value;
            }

            $valueToStorage = $this->resolveEntityId($entity, $item);
        }

        return $this->serializer->serialize($valueToStorage, 'json');
    }

    public function isTransformSupported(mixed $value, SettingDefinition $item): bool
    {
        return $this->isSupported($item);
    }

    public function reverseTransform(mixed $value, SettingDefinition $item): mixed
    {
        if (null === $value) {
            return null;
        }

        $value = $this->serializer->deserialize($value, $this->resolveSerializerType($item), 'json');

        return $this->defaultValueTransform($value, $item);
    }

    public function isReverseTransformSupported(mixed $value, SettingDefinition $item): bool
    {
        return $this->isSupported($item);
    }

    public function defaultValueTransform(mixed $defaultValue, SettingDefinition $item): mixed
    {
        /** @var class-string $entity */
        $entity = $this->getEntityClassFromType($item);

        if ($this->isMultiple($item)) {
            if (!\is_array($defaultValue)) {
                $value = [$defaultValue];
            } else {
                $value = $defaultValue;
            }

            return $this->em->getRepository($entity)->findBy(['id' => $value]);
        }

        if (\is_array($defaultValue)) {
            $value = $defaultValue[\array_key_first($defaultValue)];
        } else {
            $value = $defaultValue;
        }

        return $this->em->getRepository($entity)->find($value);
    }

    public function isDefaultValueTransformSupported(mixed $defaultValue, SettingDefinition $item): bool
    {
        return $this->isSupported($item);
    }

    private function getEntityClassFromType(SettingDefinition $item): string
    {
        \preg_match(self::REGEX_PATTERN, $item->type, $matches);

        return $matches[1];
    }

    private function isMultiple(SettingDefinition $item): bool
    {
        return \str_ends_with($item->type, '[]');
    }

    private function resolveEntityId(object $entity, SettingDefinition $item): mixed
    {
        if (!isset($item->options['id_getter'])) {
            $idGetter = 'id';
        } else {
            $idGetter = $item->options['id_getter'];
        }

        if (\is_callable($idGetter)) {
            return $idGetter($entity, $item);
        }

        return $this->getPropertyAccessor()->getValue($entity, $idGetter);
    }

    private function resolveSerializerType(SettingDefinition $item): string
    {
        if (!isset($item->options['id_type'])) {
            $type = 'int';
        } else {
            $type = $item->options['id_type'];
        }

        if ($this->isMultiple($item)) {
            $type .= '[]';
        }

        return $type;
    }

    private function isSupported(SettingDefinition $item): bool
    {
        return 1 === \preg_match(self::REGEX_PATTERN, $item->type);
    }

    private function getPropertyAccessor(): PropertyAccessorInterface
    {
        if (!isset($this->propertyAccessor)) {
            $this->propertyAccessor = new PropertyAccessor();
        }

        return $this->propertyAccessor;
    }
}
