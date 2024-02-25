<?php

declare(strict_types=1);

namespace DWalczyk\Tests\SettingBundle\Tests\Extension\Debug;

use DWalczyk\SettingBundle\Extension\Debug\SettingDataCollector;
use DWalczyk\SettingBundle\Extension\Debug\TraceableSettings;
use DWalczyk\SettingBundle\SettingOwnerInterface;
use DWalczyk\SettingBundle\SettingsInterface;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @covers \DWalczyk\SettingBundle\Extension\Debug\TraceableSettings
 */
class TraceableSettingsTest extends TestCase
{
    public function testForwardsToSettings(): void
    {
        $collector = $this->createMock(SettingDataCollector::class);
        $traceable = new TraceableSettings(new Settings(), $collector);
        $traceable->set('name', 'value', 'owner');

        $this->assertSame('value', $traceable->get('name'));
    }
}

class Settings implements SettingsInterface
{
    private mixed $value;

    public function get(string $name, SettingOwnerInterface|int|string|null $owner = null): mixed
    {
        return $this->value;
    }

    public function set(string $name, mixed $value, SettingOwnerInterface|int|string|null $owner = null): void
    {
        $this->value = $value;
    }
}
