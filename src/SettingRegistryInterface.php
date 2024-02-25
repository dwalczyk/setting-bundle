<?php

declare(strict_types=1);

namespace DWalczyk\SettingBundle;

use DWalczyk\SettingBundle\Exception\DefinitionDoesNotExistException;

interface SettingRegistryInterface
{
    /**
     * @throws DefinitionDoesNotExistException
     */
    public function getDefinition(string $name): SettingDefinition;

    /**
     * @return array<SettingDefinition>
     */
    public function getDefinitions(): array;

    /**
     * @return array<DataTransformerInterface>
     */
    public function getDataTransformers(): array;
}
