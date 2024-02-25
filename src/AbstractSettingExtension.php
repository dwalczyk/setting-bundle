<?php

declare(strict_types=1);

namespace DWalczyk\SettingBundle;

abstract class AbstractSettingExtension implements SettingExtensionInterface
{
    public function getDefinitions(): array
    {
        return [];
    }
}
