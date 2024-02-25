<?php

declare(strict_types=1);

namespace DWalczyk\SettingBundle\Extension\Core;

use DWalczyk\SettingBundle\AbstractSettingExtension;
use DWalczyk\SettingBundle\SettingDefinition;

/**
 * @internal
 */
final class FromConfigurationSettingExtension extends AbstractSettingExtension
{
    private array $definitions = [];

    public function __construct(
        array $configurationsDefinitions = []
    ) {
        foreach ($configurationsDefinitions as $name => $definition) {
            $this->definitions[] = new SettingDefinition($name, $definition['type'], $definition['default_value'], $definition['options'] ?? []);
        }
    }

    public function getDefinitions(): array
    {
        return $this->definitions;
    }
}
