<?php

declare(strict_types=1);

namespace DWalczyk\SettingBundle;

interface DataTransformerInterface
{
    /**
     * PHP -> Storage.
     */
    public function transform(mixed $value, SettingDefinition $item): mixed;

    public function isTransformSupported(mixed $value, SettingDefinition $item): bool;

    /**
     * Storage -> PHP.
     */
    public function reverseTransform(mixed $value, SettingDefinition $item): mixed;

    public function isReverseTransformSupported(mixed $value, SettingDefinition $item): bool;

    /**
     * Definition default value -> PHP.
     */
    public function defaultValueTransform(mixed $defaultValue, SettingDefinition $item): mixed;

    public function isDefaultValueTransformSupported(mixed $defaultValue, SettingDefinition $item): bool;
}
