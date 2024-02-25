<?php

declare(strict_types=1);

namespace DWalczyk\Tests\SettingBundle\Tests;

use DWalczyk\SettingBundle\DataStorageInterface;
use DWalczyk\SettingBundle\DataTransformerInterface;
use DWalczyk\SettingBundle\Exception\DataStorage\SettingNotFoundException;
use DWalczyk\SettingBundle\Exception\DefinitionDoesNotExistException;
use DWalczyk\SettingBundle\Exception\ValueReverseTransformIsNotSupportedException;
use DWalczyk\SettingBundle\Exception\ValueTransformIsNotSupportedException;
use DWalczyk\SettingBundle\SettingDefinition;
use DWalczyk\SettingBundle\SettingOwnerInterface;
use DWalczyk\SettingBundle\SettingRegistryInterface;
use DWalczyk\SettingBundle\Settings;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Cache\Adapter\NullAdapter;

/**
 * @internal
 *
 * @covers \DWalczyk\SettingBundle\Settings
 */
final class SettingsTest extends TestCase
{
    private Settings $settings;

    private DataStorageInterface $dataStorage;

    private SettingRegistryInterface $registry;

    protected function setUp(): void
    {
        $this->registry = self::createMock(SettingRegistryInterface::class);
        $this->dataStorage = self::createMock(DataStorageInterface::class);

        $this->settings = new Settings(
            $this->registry,
            $this->dataStorage,
            new NullAdapter(),
            999
        );
    }

    public function testGetReturnsValidValue(): void
    {
        $transformer = self::createMock(DataTransformerInterface::class);
        $transformer->method('reverseTransform')->willReturnCallback(function (mixed $value) {
            return $value;
        });
        $transformer->method('isReverseTransformSupported')->willReturn(true);

        $this->registry->method('getDefinition')->willReturn(new SettingDefinition('name', 'text', defaultValue: 'Default'));
        $this->registry->method('getDataTransformers')->willReturn([$transformer]);

        $this->dataStorage->method('read')->willReturn('Global');

        self::assertEquals('Global', $this->settings->get('name'));
    }

    public function testGetReturnsDefaultValue(): void
    {
        $transformer = self::createMock(DataTransformerInterface::class);
        $transformer->method('reverseTransform')->willReturnCallback(function (mixed $value) {
            return $value;
        });
        $transformer->method('isReverseTransformSupported')->willReturn(true);

        $this->registry->method('getDefinition')->willReturn(new SettingDefinition('name', 'text', defaultValue: 'Default'));
        $this->registry->method('getDataTransformers')->willReturn([$transformer]);

        $this->dataStorage->method('read')->willThrowException(new SettingNotFoundException('name', null));

        self::assertEquals('Default', $this->settings->get('name'));
    }

    #[DataProvider('getOwners')]
    public function testGetReturnsOwnerValue(mixed $owner): void
    {
        $transformer = self::createMock(DataTransformerInterface::class);
        $transformer->method('reverseTransform')->willReturnCallback(function (mixed $value) {
            return $value;
        });
        $transformer->method('isReverseTransformSupported')->willReturn(true);

        $this->registry->method('getDefinition')->willReturn(new SettingDefinition('name', 'text', defaultValue: 'Default'));
        $this->registry->method('getDataTransformers')->willReturn([$transformer]);

        $this->dataStorage->method('read')->willReturnCallback(function (string $name, ?string $ownerIdentifier) {
            if (null !== $ownerIdentifier) {
                return 'Owner';
            }

            return 'Global';
        });

        self::assertEquals('Owner', $this->settings->get('name', $owner));
    }

    public function testGetReturnsGlobalValueIfOwnershipIsNotFound(): void
    {
        $transformer = self::createMock(DataTransformerInterface::class);
        $transformer->method('reverseTransform')->willReturnCallback(function (mixed $value) {
            return $value;
        });
        $transformer->method('isReverseTransformSupported')->willReturn(true);

        $this->registry->method('getDefinition')->willReturn(new SettingDefinition('name', 'text', defaultValue: 'Default'));
        $this->registry->method('getDataTransformers')->willReturn([$transformer]);

        $this->dataStorage->method('read')->willReturnCallback(function (string $name, ?string $ownerIdentifier) {
            throw new SettingNotFoundException($name, $ownerIdentifier);
        });

        self::assertEquals('Default', $this->settings->get('name', 'John'));
    }

    public function testGetReturnsDefaultValueIfOwnershipAndGlobalNotFound(): void
    {
        $transformer = self::createMock(DataTransformerInterface::class);
        $transformer->method('reverseTransform')->willReturnCallback(function (mixed $value) {
            return $value;
        });
        $transformer->method('isReverseTransformSupported')->willReturn(true);

        $this->registry->method('getDefinition')->willReturn(new SettingDefinition('name', 'text', defaultValue: 'Default'));
        $this->registry->method('getDataTransformers')->willReturn([$transformer]);

        $this->dataStorage->method('read')->willReturnCallback(function (string $name, ?string $ownerIdentifier) {
            if (null !== $ownerIdentifier) {
                throw new SettingNotFoundException($name, $ownerIdentifier);
            }

            return 'Global';
        });

        self::assertEquals('Global', $this->settings->get('name', 'John'));
    }

    public function testGetThrowsDefinitionDoesNotExistExceptionIfSettingNotDefined(): void
    {
        $this->registry->method('getDefinition')->willThrowException(new DefinitionDoesNotExistException('name'));

        $this->expectException(DefinitionDoesNotExistException::class);

        $this->settings->get('name');
    }

    public function testGetThrowsValueReverseTransformIsNotSupportedExceptionIfNonSupported(): void
    {
        $this->registry->method('getDataTransformers')->willReturn([]);
        $this->registry->method('getDefinition')->willReturn(new SettingDefinition('name', 'text'));

        $this->expectException(ValueReverseTransformIsNotSupportedException::class);

        $this->settings->get('name');
    }

    public function testSetWithNullAsValueWillBeProcessed(): void
    {
        $transformer = self::createMock(DataTransformerInterface::class);
        $transformer->method('transform')->willReturnCallback(function (mixed $value) {
            return $value;
        });
        $transformer->method('isTransformSupported')->willReturn(true);

        $this->registry->method('getDefinition')->willReturn(new SettingDefinition('name', 'text', defaultValue: 'Default'));
        $this->registry->method('getDataTransformers')->willReturn([$transformer]);

        $this->dataStorage->method('write')->willReturnCallback(function (string $name, ?string $value, ?string $ownerIdentifier) {
            self::assertNull($value);
        });

        $this->settings->set('name', null);
    }

    public function testSetSettingNameWillBeSaved(): void
    {
        $transformer = self::createMock(DataTransformerInterface::class);
        $transformer->method('transform')->willReturnCallback(function (mixed $value) {
            return $value;
        });
        $transformer->method('isTransformSupported')->willReturn(true);

        $this->registry->method('getDefinition')->willReturn(new SettingDefinition('name', 'text', defaultValue: 'Default'));
        $this->registry->method('getDataTransformers')->willReturn([$transformer]);

        $this->dataStorage->method('write')->willReturnCallback(function (string $name, ?string $value, ?string $ownerIdentifier) {
            self::assertEquals('name', $name);
        });

        $this->settings->set('name', 'xx');
    }

    #[DataProvider('getOwners')]
    public function testSetOwnerWillBeSaved(mixed $owner): void
    {
        $transformer = self::createMock(DataTransformerInterface::class);
        $transformer->method('transform')->willReturnCallback(function (mixed $value) {
            return $value;
        });
        $transformer->method('isTransformSupported')->willReturn(true);

        $this->registry->method('getDefinition')->willReturn(new SettingDefinition('name', 'text', defaultValue: 'Default'));
        $this->registry->method('getDataTransformers')->willReturn([$transformer]);

        $this->dataStorage->method('write')->willReturnCallback(function (string $name, ?string $value, ?string $ownerIdentifier) use ($owner) {
            self::assertEquals($owner instanceof SettingOwnerInterface ? $owner->getSettingIdentifier() : $owner, $ownerIdentifier);
        });

        $this->settings->set('name', 'xx', $owner);
    }

    public function testSetThrowsDefinitionDoesNotExistExceptionIfSettingNotDefined(): void
    {
        $this->registry->method('getDefinition')->willThrowException(new DefinitionDoesNotExistException('name'));

        $this->expectException(DefinitionDoesNotExistException::class);

        $this->settings->set('name', 'x');
    }

    public function testSetThrowsValueTransformIsNotSupportedExceptionIfNonSupported(): void
    {
        $this->registry->method('getDataTransformers')->willReturn([]);
        $this->registry->method('getDefinition')->willReturn(new SettingDefinition('name', 'text'));

        $this->expectException(ValueTransformIsNotSupportedException::class);

        $this->settings->set('name', 'x');
    }

    public static function getOwners(): array
    {
        return [
            ['John'],
            [new User('John')],
            [555],
        ];
    }
}

class User implements SettingOwnerInterface
{
    public function __construct(private readonly int|string $id)
    {
    }

    public function getSettingIdentifier(): string
    {
        return (string) $this->id;
    }
}
