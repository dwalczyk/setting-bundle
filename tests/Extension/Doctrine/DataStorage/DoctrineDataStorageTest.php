<?php

declare(strict_types=1);

namespace DWalczyk\Tests\SettingBundle\Tests\Extension\Doctrine\DataStorage;

use Doctrine\ORM\EntityManagerInterface;
use DWalczyk\SettingBundle\Exception\DataStorage\SettingNotFoundException;
use DWalczyk\SettingBundle\Extension\Doctrine\DataStorage\DoctrineDataStorage;
use DWalczyk\SettingBundle\Extension\Doctrine\Entity\Setting;
use DWalczyk\SettingBundle\Extension\Doctrine\Repository\SettingRepositoryInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @covers \DWalczyk\SettingBundle\Extension\Doctrine\DataStorage\DoctrineDataStorage
 */
final class DoctrineDataStorageTest extends TestCase
{
    private DoctrineDataStorage $storage;

    private EntityManagerInterface $em;

    private SettingRepositoryInterface $repository;

    protected function setUp(): void
    {
        $this->em = self::createMock(EntityManagerInterface::class);
        $this->repository = self::createMock(SettingRepositoryInterface::class);

        $this->storage = new DoctrineDataStorage($this->repository, $this->em);
    }

    public function testReadThrowSettingNotFoundExceptionIfNotFound(): void
    {
        $this->repository->method('find')->willReturn(null);

        self::expectException(SettingNotFoundException::class);

        $this->storage->read('name', null);
    }

    public function testReadWithOwnerThrowSettingNotFoundExceptionIfNotFound(): void
    {
        $this->repository->method('find')->willReturn(null);

        self::expectException(SettingNotFoundException::class);

        $this->storage->read('name', 'owner');
    }

    #[DataProvider('getTestValues')]
    public function testReadWillReturnValidEntityValue(mixed $value): void
    {
        $setting = new Setting('name', null);
        $setting->setValue($value);
        $this->repository->method('find')->willReturn($setting);

        self::assertEquals($value, $this->storage->read('name', null));
    }

    #[DataProvider('getTestValues')]
    public function testReadWithOwnerWillReturnValidEntityValue(mixed $value): void
    {
        $setting = new Setting('name', 'owner');
        $setting->setValue($value);
        $this->repository->method('find')->willReturn($setting);

        self::assertEquals($value, $this->storage->read('name', 'owner'));
    }

    public static function getTestValues(): array
    {
        return [
            [null],
            ['something'],
        ];
    }

    #[DataProvider('getWriteCreateSettingTestParams')]
    public function testWriteWillCreateSettingIfNoExist(mixed $expected, ?string $ownerId, callable $valueGenerator): void
    {
        $this->repository->method('find')->willReturn(null);
        $this->em->method('persist')->with(self::callback(function (Setting $setting) use ($expected, $valueGenerator): bool {
            self::assertEquals($expected, $valueGenerator($setting));

            return true;
        }));

        $this->storage->write('name', 'value', $ownerId);
    }

    public static function getWriteCreateSettingTestParams(): array
    {
        return [
            ['value', null, function (Setting $setting) {
                return $setting->getValue();
            }],
            ['name', null, function (Setting $setting) {
                return $setting->getName();
            }],
            [null, null, function (Setting $setting) {
                return $setting->getOwnerIdentifier();
            }],
            ['owner', 'owner', function (Setting $setting) {
                return $setting->getOwnerIdentifier();
            }],
        ];
    }

    public function testWriteWillUpdateValueIfSettingExist(): void
    {
        $setting = new Setting('name', null);
        $setting->setValue('value');
        $this->repository->method('find')->willReturn($setting);

        $this->storage->write('name', 'new-value', null);

        self::assertEquals('new-value', $setting->getValue());
    }
}
