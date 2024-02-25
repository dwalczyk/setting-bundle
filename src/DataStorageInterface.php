<?php

declare(strict_types=1);

namespace DWalczyk\SettingBundle;

use DWalczyk\SettingBundle\Exception\DataStorage\SettingNotFoundException;

interface DataStorageInterface
{
    /**
     * @throws SettingNotFoundException
     */
    public function read(string $name, ?string $ownerIdentifier): ?string;

    public function write(string $name, ?string $value, ?string $ownerIdentifier): void;
}
