<?php

declare(strict_types=1);

namespace DWalczyk\Tests\SettingBundle\Tests;

use DWalczyk\SettingBundle\Exception\DefinitionDoesNotExistException;
use DWalczyk\SettingBundle\Extension\Core\DataTransformer\NativePhpSerializerDataTransformer;
use DWalczyk\SettingBundle\SettingDefinition;
use DWalczyk\SettingBundle\SettingRegistry;
use DWalczyk\Tests\SettingBundle\Tests\Fixture\TestExtension;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @covers \DWalczyk\SettingBundle\SettingRegistry
 */
final class SettingRegistryTest extends TestCase
{
    private SettingRegistry $registry;

    private TestExtension $testExtension;

    protected function setUp(): void
    {
        $this->registry = new SettingRegistry([$this->testExtension = new TestExtension()], []);
    }

    public function testConstructorInvalidTypedExtensionsWillThrowException(): void
    {
        self::expectException(\Exception::class);

        new SettingRegistry([new \stdClass()], []);
    }

    public function testConstructorInvalidTypedDaTransformersWillThrowException(): void
    {
        self::expectException(\Exception::class);

        new SettingRegistry([], [new \stdClass()]);
    }

    public function testGetDefinitionReturnsValidDefinitionIfExist(): void
    {
        $this->testExtension->addDefinition(new SettingDefinition('system_mode', 'text'));

        /* @noinspection PhpUnhandledExceptionInspection */
        self::assertEquals('system_mode', $this->registry->getDefinition('system_mode')->name);
    }

    public function testGetDefinitionThrowDefinitionDoesNotExistExceptionIfNoExist(): void
    {
        self::expectException(DefinitionDoesNotExistException::class);

        $this->registry->getDefinition('system_mode');
    }

    #[DataProvider('getGetDefinitionsValidDefinitionsDataProvider')]
    public function testGetDefinitionsReturnsValidDefinitions(string $name): void
    {
        $this->testExtension->addDefinition(new SettingDefinition('system_mode', 'text'));
        $this->testExtension->addDefinition(new SettingDefinition('enabled', 'bool'));

        $registryDefinitionsNames = \array_values(\array_map(function (SettingDefinition $definition) {
            return $definition->name;
        }, $this->registry->getDefinitions()));

        self::assertContains($name, $registryDefinitionsNames);
    }

    public static function getGetDefinitionsValidDefinitionsDataProvider(): array
    {
        return [
            ['system_mode'],
            ['enabled'],
        ];
    }

    public function testGetTransformersReturnsValidDataTransformers(): void
    {
        $registry = new SettingRegistry([$this->testExtension = new TestExtension()], [new NativePhpSerializerDataTransformer()]);

        $has = false;
        $registryDataTransformers = $registry->getDataTransformers();
        foreach ($registryDataTransformers as $dataTransformer) {
            if ($dataTransformer instanceof NativePhpSerializerDataTransformer) {
                $has = true;
            }
        }

        self::assertTrue($has, 'getDataTransformers() does not return NativePhpSerializerDataTransformer');
    }
}
