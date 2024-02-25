<?php

declare(strict_types=1);

namespace DWalczyk\SettingBundle;

use DWalczyk\SettingBundle\Exception\DefinitionDoesNotExistException;
use DWalczyk\SettingBundle\Exception\ValueReverseTransformIsNotSupportedException;
use DWalczyk\SettingBundle\Exception\ValueTransformIsNotSupportedException;

interface SettingsInterface
{
    /**
     * @throws DefinitionDoesNotExistException
     * @throws ValueReverseTransformIsNotSupportedException
     */
    public function get(string $name, int|string|SettingOwnerInterface|null $owner = null): mixed;

    /**
     * @throws DefinitionDoesNotExistException
     * @throws ValueTransformIsNotSupportedException
     */
    public function set(string $name, mixed $value, int|string|SettingOwnerInterface|null $owner = null): void;
}
