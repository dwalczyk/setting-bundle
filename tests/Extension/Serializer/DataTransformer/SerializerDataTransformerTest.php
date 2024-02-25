<?php

declare(strict_types=1);

namespace DWalczyk\Tests\SettingBundle\Tests\Extension\Serializer\DataTransformer;

use DWalczyk\SettingBundle\Extension\Serializer\DataTransformer\SerializerDataTransformer;
use DWalczyk\SettingBundle\SettingDefinition;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * @internal
 *
 * @covers \DWalczyk\SettingBundle\Extension\Serializer\DataTransformer\SerializerDataTransformer
 */
final class SerializerDataTransformerTest extends TestCase
{
    private SerializerDataTransformer $dataTransformer;

    protected function setUp(): void
    {
        $this->dataTransformer = new SerializerDataTransformer(
            new Serializer([new ArrayDenormalizer()], [new JsonEncoder()])
        );
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

            ['int[]', null],
            ['int[]', [1, 3]],
            ['string[]', ['Dog', 'Cat']],
            ['string[]', ['dog' => 'Dog', 'cat' => 'Cat']],
        ];
    }
}
