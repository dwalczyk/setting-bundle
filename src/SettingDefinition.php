<?php

declare(strict_types=1);

namespace DWalczyk\SettingBundle;

final readonly class SettingDefinition
{
    public function __construct(
        public string $name,
        public string $type,
        public mixed $defaultValue = null,
        public array $options = []
    ) {
    }
}
