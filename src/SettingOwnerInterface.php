<?php

declare(strict_types=1);

namespace DWalczyk\SettingBundle;

interface SettingOwnerInterface
{
    public function getSettingIdentifier(): string;
}
