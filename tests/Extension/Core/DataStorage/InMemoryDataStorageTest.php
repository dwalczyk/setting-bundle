<?php

declare(strict_types=1);

namespace DWalczyk\Tests\SettingBundle\Tests\Extension\Core\DataStorage;

use DWalczyk\Tests\SettingBundle\Tests\DataStorageTestTrait;
use DWalczyk\Tests\SettingBundle\Tests\Fixture\DataStorage\InMemoryDataStorage;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @covers \DWalczyk\Tests\SettingBundle\Tests\Fixture\DataStorage\InMemoryDataStorage
 */
final class InMemoryDataStorageTest extends TestCase
{
    use DataStorageTestTrait;

    private InMemoryDataStorage $storage;

    protected function setUp(): void
    {
        $this->storage = new InMemoryDataStorage();
    }
}
