<?php

declare(strict_types=1);

namespace DWalczyk\SettingBundle\Extension\Core\DataTransformer;

use DWalczyk\SettingBundle\AbstractDataTransformer;
use DWalczyk\SettingBundle\SettingDefinition;

final class NativePhpSerializerDataTransformer extends AbstractDataTransformer
{
    public function transform(mixed $value, SettingDefinition $item): ?string
    {
        if (null === $value) {
            return null;
        }

        return \serialize($value);
    }

    public function reverseTransform(mixed $value, SettingDefinition $item): mixed
    {
        if (null === $value) {
            return null;
        }

        return \unserialize($value);
    }
}
