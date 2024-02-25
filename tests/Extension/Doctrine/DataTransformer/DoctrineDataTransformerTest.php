<?php

declare(strict_types=1);

namespace DWalczyk\Tests\SettingBundle\Tests\Extension\Doctrine\DataTransformer;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use DWalczyk\SettingBundle\Extension\Doctrine\DataTransformer\DoctrineDataTransformer;
use DWalczyk\SettingBundle\SettingDefinition;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * @internal
 *
 * @covers \DWalczyk\SettingBundle\Extension\Doctrine\DataTransformer\DoctrineDataTransformer
 */
final class DoctrineDataTransformerTest extends TestCase
{
    private DoctrineDataTransformer $dataTransformer;

    private EntityManagerInterface $em;

    protected function setUp(): void
    {
        $this->em = $this->createMock(EntityManagerInterface::class);

        $this->dataTransformer = new DoctrineDataTransformer(
            $this->em,
            new Serializer([new ArrayDenormalizer()], [new JsonEncoder()])
        );
    }

    public function testTransformReturnNullIfValueIsNull(): void
    {
        self::assertNull($this->dataTransformer->transform(null, self::createSampleSettingDefinition()));
    }

    public function testTransformEntityToItsId(): void
    {
        $value = $this->dataTransformer->transform(new Entity(1, 999), self::createSampleSettingDefinition());

        self::assertSame(1, \json_decode($value));
    }

    public function testTransformEntitiesToSingleIdIfArrayProvided(): void
    {
        $entities = [
            new Entity(1, 999),
            new Entity(2, 1998),
            new Entity(3, 2997),
        ];

        $value = $this->dataTransformer->transform($entities, self::createSampleSettingDefinition());

        self::assertSame(1, \json_decode($value));
    }

    public function testTransformEntityToItsIdWithOptionIdGetterAsString(): void
    {
        $value = $this->dataTransformer->transform(new Entity(1, 999), self::createSampleSettingDefinition(options: [
            'id_getter' => 'secondId',
        ]));

        self::assertSame(999, \json_decode($value));
    }

    public function testTransformEntityToItsIdWithOptionIdGetterAsCallable(): void
    {
        $value = $this->dataTransformer->transform(new Entity(1, 999), self::createSampleSettingDefinition(options: [
            'id_getter' => function (Entity $entity) {
                return $entity->getSecondId();
            },
        ]));

        self::assertSame(999, \json_decode($value));
    }

    public function testTransformEntitiesToTheirsId(): void
    {
        $entities = [
            new Entity(1, 999),
            new Entity(2, 1998),
            new Entity(3, 2997),
        ];

        $value = $this->dataTransformer->transform($entities, self::createSampleSettingDefinition(multiple: true));

        self::assertSame([1, 2, 3], \json_decode($value));
    }

    public function testTransformEntityToArrayOfIdsIfSingleInsteadOfArrayProvided(): void
    {
        $value = $this->dataTransformer->transform(new Entity(1, 999), self::createSampleSettingDefinition(multiple: true));

        self::assertSame([1], \json_decode($value));
    }

    public function testTransformEntitiesToTheirsIdWithOptionIdGetterAsString(): void
    {
        $entities = [
            new Entity(1, 999),
            new Entity(2, 1998),
            new Entity(3, 2997),
        ];

        $value = $this->dataTransformer->transform($entities, self::createSampleSettingDefinition(multiple: true, options: [
            'id_getter' => 'secondId',
        ]));

        self::assertSame([999, 1998, 2997], \json_decode($value));
    }

    public function testTransformEntitiesToTheirsIdWithOptionIdGetterAsCallable(): void
    {
        $entities = [
            new Entity(1, 999),
            new Entity(2, 1998),
            new Entity(3, 2997),
        ];

        $value = $this->dataTransformer->transform($entities, self::createSampleSettingDefinition(multiple: true, options: [
            'id_getter' => function (Entity $entity) {
                return $entity->getSecondId();
            },
        ]));

        self::assertSame([999, 1998, 2997], \json_decode($value));
    }

    public function testReverseTransformReturnsNullIfNullValueProvider(): void
    {
        self::assertNull($this->dataTransformer->reverseTransform(null, self::createSampleSettingDefinition()));
    }

    public function testReverseTransformReturnsValidEntity(): void
    {
        $repository = $this->createMock(ObjectRepository::class);
        $this->em->method('getRepository')
            ->willReturn($repository)
        ;
        $repository->method('find')->willReturn($entity = new Entity(1, 9999));

        $results = $this->dataTransformer->reverseTransform(1, self::createSampleSettingDefinition());

        self::assertEquals($entity, $results);
    }

    public function testReverseTransformOnMultipleReturnsValidEntities(): void
    {
        $repository = $this->createMock(ObjectRepository::class);
        $this->em->method('getRepository')
            ->willReturn($repository)
        ;
        $repository->method('findBy')->willReturn($entities = [
            new Entity(2, 9999),
            new Entity(3, 19998),
            new Entity(4, 29997),
        ]);

        $results = $this->dataTransformer->reverseTransform('[2,3,4]', self::createSampleSettingDefinition(multiple: true));

        self::assertEquals($entities, $results);
    }

    public function testReverseTransformOnMultipleReturnsValidEntitiesWithOptionIdType(): void
    {
        $repository = $this->createMock(ObjectRepository::class);
        $this->em->method('getRepository')
            ->willReturn($repository)
        ;
        $repository->method('findBy')->willReturn($entities = [
            new Entity(2, 9999),
            new Entity(3, 19998),
            new Entity(4, 29997),
        ]);

        $results = $this->dataTransformer->reverseTransform('["2","3","4"]', self::createSampleSettingDefinition(multiple: true, options: ['id_type' => 'string']));

        self::assertEquals($entities, $results);
    }

    public function testDefaultValueTransformReturnsValidEntity(): void
    {
        $repository = $this->createMock(ObjectRepository::class);
        $this->em->method('getRepository')
            ->willReturn($repository)
        ;
        $repository->method('find')->willReturn($entity = new Entity(1, 9999));

        $results = $this->dataTransformer->defaultValueTransform(1, self::createSampleSettingDefinition());

        self::assertEquals($entity, $results);
    }

    public function testDefaultValueTransformOnMultipleReturnsValidEntities(): void
    {
        $repository = $this->createMock(ObjectRepository::class);
        $this->em->method('getRepository')
            ->willReturn($repository)
        ;
        $repository->method('findBy')->willReturn($entities = [
            new Entity(2, 9999),
            new Entity(3, 19998),
            new Entity(4, 29997),
        ]);

        $results = $this->dataTransformer->defaultValueTransform([2, 3, 4], self::createSampleSettingDefinition(multiple: true));

        self::assertEquals($entities, $results);
    }

    #[DataProvider('getTypeAndIsTransformSupported')]
    public function testIsTransformSupportedReturnsValidValue(string $type, bool $isSupported)
    {
        self::assertEquals($isSupported, $this->dataTransformer->isTransformSupported(1, self::createSampleSettingDefinition(type: $type)));
    }

    #[DataProvider('getTypeAndIsTransformSupported')]
    public function testIsReverseTransformSupportedReturnsValidValue(string $type, bool $isSupported)
    {
        self::assertEquals($isSupported, $this->dataTransformer->isReverseTransformSupported(1, self::createSampleSettingDefinition(type: $type)));
    }

    #[DataProvider('getTypeAndIsTransformSupported')]
    public function testIsDefaultValueTransformSupportedReturnsValidValue(string $type, bool $isSupported)
    {
        self::assertEquals($isSupported, $this->dataTransformer->isDefaultValueTransformSupported(1, self::createSampleSettingDefinition(type: $type)));
    }

    public static function getTypeAndIsTransformSupported(): array
    {
        return [
            ['doctrine-entity<Entity>',  true],
            ['doctrine-entity<Entity>[]',  true],
            ['string', false],
            ['string[]', false],
            ['int', false],
            ['int[]', false],
        ];
    }

    private static function createSampleSettingDefinition(string $entity = 'Entity', ?string $type = null, bool $multiple = false, array $options = []): SettingDefinition
    {
        if (null === $type) {
            $type = \sprintf('doctrine-entity<%s>', $entity);
            if (true === $multiple) {
                $type .= '[]';
            }
        }

        return new SettingDefinition('name', $type, options: $options);
    }
}

class Entity
{
    private int $id;

    private int $secondId;

    public function __construct(int $id, int $secondId)
    {
        $this->id = $id;
        $this->secondId = $secondId;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getSecondId(): int
    {
        return $this->secondId;
    }
}
