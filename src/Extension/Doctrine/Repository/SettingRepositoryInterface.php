<?php

declare(strict_types=1);

namespace DWalczyk\SettingBundle\Extension\Doctrine\Repository;

use DWalczyk\SettingBundle\Extension\Doctrine\Entity\Setting;

interface SettingRepositoryInterface
{
    public function find(string $name, int|string|null $ownerIdentifier): ?Setting;
}
