<?php

declare(strict_types=1);

namespace DWalczyk\Tests\SettingBundle\Tests\Extension\Core\DataTransformer;

use DWalczyk\SettingBundle\Extension\Core\DataTransformer\NativePhpSerializerDataTransformer;
use DWalczyk\SettingBundle\SettingDefinition;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @covers \DWalczyk\SettingBundle\Extension\Core\DataTransformer\NativePhpSerializerDataTransformer
 */
final class NativePhpSerializerDataTransformerTest extends TestCase
{
    private NativePhpSerializerDataTransformer $dataTransformer;

    protected function setUp(): void
    {
        $this->dataTransformer = new NativePhpSerializerDataTransformer();
    }

    #[DataProvider('getValuesAndDefinitionTypes')]
    public function testTransformAndReverseTransformWillReturnInitialValue(string $definitionType, mixed $initialValue): void
    {
        $definition = new SettingDefinition('name', $definitionType);

        $value = $this->dataTransformer->reverseTransform(
            $this->dataTransformer->transform($initialValue, $definition),
            $definition
        );

        self::assertSame($initialValue, $value);
    }

    public static function getValuesAndDefinitionTypes(): array
    {
        return [
            ['string', 'Lorem ipsum'],
            ['string', null],

            ['int', 333],
            ['integer', 555],
            ['integer', null],

            ['boolean', true],
            ['bool', false],
            ['bool', null],

            ['float', 1234.5678],
            ['float', null],

            ['array', null],
            ['array', ['Dog', 'Cat']],
            ['array', ['dog' => 'Dog', 'cat' => 'Cat']],
        ];
    }
}
