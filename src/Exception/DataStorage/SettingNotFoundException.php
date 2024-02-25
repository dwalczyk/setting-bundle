<?php

declare(strict_types=1);

namespace DWalczyk\SettingBundle\Exception\DataStorage;

final class SettingNotFoundException extends \Exception
{
    public function __construct(string $name, int|string|null $ownerIdentifier)
    {
        parent::__construct(\sprintf('Setting named "%s" with owner identifier "%s" does not exist.', $name, (string) ($ownerIdentifier ?? 'null')));
    }
}
