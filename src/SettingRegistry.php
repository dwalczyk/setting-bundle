<?php

declare(strict_types=1);

namespace DWalczyk\SettingBundle;

use DWalczyk\SettingBundle\Exception\DefinitionDoesNotExistException;
use Webmozart\Assert\Assert;

final class SettingRegistry implements SettingRegistryInterface
{
    private array $definitions;

    /**
     * @var array<DataTransformerInterface>
     */
    private array $dataTransformers;

    public function __construct(
        /** @var array<SettingExtensionInterface> $extensions */
        private readonly iterable $extensions,
        /* @var array<DataTransformerInterface> $dataTransformers */
        iterable $dataTransformers
    ) {
        Assert::allIsInstanceOf($this->extensions, SettingExtensionInterface::class);
        Assert::allIsInstanceOf($dataTransformers, DataTransformerInterface::class);

        $this->dataTransformers = [...$dataTransformers];
    }

    public function getDefinition(string $name): SettingDefinition
    {
        return $this->getDefinitions()[$name] ?? throw new DefinitionDoesNotExistException($name);
    }

    public function getDefinitions(): array
    {
        if (!isset($this->definitions)) {
            $definitions = [];
            foreach ($this->extensions as $extension) {
                foreach ($extension->getDefinitions() as $definition) {
                    $definitions[$definition->name] = $definition;
                }
            }

            $this->definitions = $definitions;
        }

        return $this->definitions;
    }

    public function getDataTransformers(): array
    {
        return $this->dataTransformers;
    }
}
