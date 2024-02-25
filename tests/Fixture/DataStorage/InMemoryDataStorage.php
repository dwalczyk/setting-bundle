<?php

declare(strict_types=1);

namespace DWalczyk\Tests\SettingBundle\Tests\Fixture\DataStorage;

use DWalczyk\SettingBundle\DataStorageInterface;
use DWalczyk\SettingBundle\Exception\DataStorage\SettingNotFoundException;

final class InMemoryDataStorage implements DataStorageInterface
{
    private array $memory = [];
    private array $memoryOwners = [];

    public function read(string $name, ?string $ownerIdentifier): ?string
    {
        if (null !== $ownerIdentifier) {
            if (!isset($this->memoryOwners[$name]) || !\array_key_exists($ownerIdentifier, $this->memoryOwners[$name])) {
                throw new SettingNotFoundException($name, $ownerIdentifier);
            }

            return $this->memoryOwners[$name][$ownerIdentifier];
        }

        if (!\array_key_exists($name, $this->memory)) {
            throw new SettingNotFoundException($name, $ownerIdentifier);
        }

        return $this->memory[$name];
    }

    public function write(string $name, ?string $value, ?string $ownerIdentifier): void
    {
        if (null !== $ownerIdentifier) {
            $this->memoryOwners[$name][$ownerIdentifier] = $value;

            return;
        }

        $this->memory[$name] = $value;
    }
}
