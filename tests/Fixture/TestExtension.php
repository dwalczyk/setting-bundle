<?php

declare(strict_types=1);

namespace DWalczyk\Tests\SettingBundle\Tests\Fixture;

use DWalczyk\SettingBundle\SettingDefinition;
use DWalczyk\SettingBundle\SettingExtensionInterface;

/**
 * @internal
 */
final class TestExtension implements SettingExtensionInterface
{
    public function __construct(
        private array $definitions = [],
    ) {
    }

    public function getDefinitions(): array
    {
        return $this->definitions;
    }

    public function addDefinition(SettingDefinition $definition): self
    {
        $this->definitions[$definition->name] = $definition;

        return $this;
    }

    public function removeDefinition(string $name): self
    {
        unset($this->definitions[$name]);

        return $this;
    }
}
