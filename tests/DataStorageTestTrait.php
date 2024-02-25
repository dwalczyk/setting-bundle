<?php

declare(strict_types=1);

namespace DWalczyk\Tests\SettingBundle\Tests;

use DWalczyk\SettingBundle\Exception\DataStorage\SettingNotFoundException;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * @internal
 */
trait DataStorageTestTrait
{
    #[DataProvider('getNameOwnerValueCombinations')]
    public function testReadWillThrowSettingNotFoundExceptionIfNotFound(string $name, string|null $ownerIdentifier): void
    {
        $this->expectException(SettingNotFoundException::class);

        $this->storage->read($name, $ownerIdentifier);
    }

    #[DataProvider('getNameOwnerValueCombinations')]
    public function testWriteAndRead(string $name, string|null $ownerIdentifier, string $value): void
    {
        $this->storage->write($name, $value, $ownerIdentifier);

        self::assertEquals($value, $this->storage->read($name, $ownerIdentifier));
    }

    public static function getNameOwnerValueCombinations(): array
    {
        return [
            ['abc', null, 'test1'],
            ['abc', '1', 'test2'],
        ];
    }
}
