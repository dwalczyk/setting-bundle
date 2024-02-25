<?php

declare(strict_types=1);

namespace DWalczyk\SettingBundle;

abstract class AbstractDataTransformer implements DataTransformerInterface
{
    public function isTransformSupported(mixed $value, SettingDefinition $item): bool
    {
        return true;
    }

    public function isReverseTransformSupported(mixed $value, SettingDefinition $item): bool
    {
        return true;
    }

    public function defaultValueTransform(mixed $defaultValue, SettingDefinition $item): mixed
    {
        return $defaultValue;
    }

    public function isDefaultValueTransformSupported(mixed $defaultValue, SettingDefinition $item): bool
    {
        return true;
    }
}
