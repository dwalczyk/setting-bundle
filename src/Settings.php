<?php

declare(strict_types=1);

namespace DWalczyk\SettingBundle;

use DWalczyk\SettingBundle\Exception\DefinitionDoesNotExistException;
use DWalczyk\SettingBundle\Exception\ValueReverseTransformIsNotSupportedException;
use DWalczyk\SettingBundle\Exception\ValueTransformIsNotSupportedException;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\InvalidArgumentException;
use Symfony\Contracts\Cache\CacheInterface;

final readonly class Settings implements SettingsInterface
{
    /**
     * @param int $cacheLifeTime - in seconds
     */
    public function __construct(
        private SettingRegistryInterface $registry,
        private DataStorageInterface $dataStorage,
        private CacheInterface $cache,
        private int $cacheLifeTime = 3600 * 24 * 30
    ) {
    }

    /**
     * @throws DefinitionDoesNotExistException
     * @throws ValueReverseTransformIsNotSupportedException
     */
    public function get(string $name, int|string|SettingOwnerInterface|null $owner = null): mixed
    {
        $definition = $this->registry->getDefinition($name);
        $resolvedOwner = $this->resolveOwner($owner);

        return $this->doGet($definition, $resolvedOwner);
    }

    private function doGet(SettingDefinition $definition, string|null $owner): mixed
    {
        $name = $definition->name;
        try {
            /** @var string|false|null $storageValue */
            $storageValue = $this->cache->get($this->generateCacheKey($name, $owner), function (CacheItemInterface $item) use ($name, $owner) {
                $item->expiresAfter($this->cacheLifeTime);
                try {
                    return $this->dataStorage->read($name, $owner);
                } catch (Exception\DataStorage\SettingNotFoundException) {
                    return false;
                }
            });
        } catch (InvalidArgumentException $e) {
            throw new \RuntimeException(\sprintf('Exception while fetching setting "%s" from cache: %s', $name, $e));
        }

        if (false === $storageValue) {
            if (null !== $owner) {
                return $this->get($name);
            }

            return $this->getDefaultValue($definition);
        }

        foreach ($this->registry->getDataTransformers() as $dataTransformer) {
            if ($dataTransformer->isReverseTransformSupported($storageValue, $definition)) {
                return $dataTransformer->reverseTransform($storageValue, $definition);
            }
        }

        throw new ValueReverseTransformIsNotSupportedException($storageValue, $definition);
    }

    /**
     * @throws DefinitionDoesNotExistException
     * @throws ValueTransformIsNotSupportedException
     */
    public function set(string $name, mixed $value, int|string|SettingOwnerInterface|null $owner = null): void
    {
        $definition = $this->registry->getDefinition($name);
        $resolvedOwner = $this->resolveOwner($owner);

        $this->doSet($definition, $value, $resolvedOwner);
    }

    private function doSet(SettingDefinition $definition, mixed $value, string|null $owner): void
    {
        $transformed = false;
        $transformedValue = null;

        foreach ($this->registry->getDataTransformers() as $dataTransformer) {
            if ($dataTransformer->isTransformSupported($value, $definition)) {
                $transformedValue = $dataTransformer->transform($value, $definition);
                $transformed = true;
                break;
            }
        }

        if (false === $transformed) {
            throw new ValueTransformIsNotSupportedException($value, $definition);
        }

        $this->dataStorage->write($definition->name, $transformedValue, $owner);

        try {
            $this->cache->delete($this->generateCacheKey($definition->name, $owner));
        } catch (InvalidArgumentException $e) {
            throw new \RuntimeException(\sprintf('Exception while delete setting "%s" from cache: %s', $definition->name, $e));
        }
    }

    private function getDefaultValue(SettingDefinition $definition): mixed
    {
        foreach ($this->registry->getDataTransformers() as $dataTransformer) {
            if ($dataTransformer->isDefaultValueTransformSupported($definition->defaultValue, $definition)) {
                return $dataTransformer->defaultValueTransform($definition->defaultValue, $definition);
            }
        }

        return $definition->defaultValue;
    }

    private function resolveOwner(int|string|SettingOwnerInterface|null $owner): string|null
    {
        if (null === $owner) {
            return null;
        }

        if ($owner instanceof SettingOwnerInterface) {
            $owner = $owner->getSettingIdentifier();
        }

        return (string) $owner;
    }

    private function generateCacheKey(string $name, string|null $ownerId): string
    {
        if (null === $ownerId) {
            return $name;
        }

        return \sprintf('%s_%s', $name, $ownerId);
    }
}
