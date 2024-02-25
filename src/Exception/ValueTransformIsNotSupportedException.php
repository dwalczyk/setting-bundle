<?php

declare(strict_types=1);

namespace DWalczyk\SettingBundle\Exception;

use DWalczyk\SettingBundle\SettingDefinition;

final class ValueTransformIsNotSupportedException extends \Exception
{
    public function __construct(mixed $value, SettingDefinition $item)
    {
        parent::__construct(\sprintf('Unsupported transform for setting named "%s" with value "%s".', $item->name, \print_r($value, true)));
    }
}
