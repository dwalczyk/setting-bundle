<?php

declare(strict_types=1);

namespace DWalczyk\SettingBundle;

interface SettingExtensionInterface
{
    /**
     * @return array<SettingDefinition>
     */
    public function getDefinitions(): array;
}
