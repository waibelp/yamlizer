<?php
/**
 * This file is part of the Yamlizer package.
 *
 * (c) Pierre Waibel <waibelp85@googlemail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yamlizer\Tests\Serialization;

use Doctrine\Common\Annotations\AnnotationReader;
use Yamlizer\Metadata\MetadataFactory;
use Yamlizer\Metadata\PropertyMetadata;
use Yamlizer\Serialization\ArraySerializer;
use Yamlizer\Serialization\Context;
use Yamlizer\Tests\Entity\DateTimeEntity;
use Yamlizer\Tests\Entity\ObjectEntity;
use Yamlizer\Tests\Entity\PrimitiveEntity;

/**
 * Class ArraySerializerTest
 *
 * @package Yamlizer\Tests\Serialization
 */
class ArraySerializerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ArraySerializer
     */
    protected $serializer;

    /**
     * @var MetadataFactory
     */
    protected $factory;

    protected function setUp()
    {
        parent::setUp();

        $this->factory    = new MetadataFactory(new AnnotationReader());
        $this->serializer = new ArraySerializer($this->factory);
        $this->serializer->setDefaultContext(new Context());
    }

    protected function tearDown()
    {
        $this->serializer = null;
        $this->factory    = null;

        parent::tearDown();
    }

    public function testSerialize()
    {
        // Primitives
        $object = new PrimitiveEntity();
        $this->assertEquals(
            [
                'integer' => null,
                'string'  => null,
                'array'   => [1, '2', true, false],
                'boolean' => true,
                'float'   => 13.37,
            ],
            $this->serializer->serialize($object, PrimitiveEntity::class)
        );

        // \DateTime instances with different formats
        $object = new DateTimeEntity();
        $object->setDatetimeA(new \DateTime('2016-01-01 00:00:00'));
        $object->setDatetimeB(new \DateTime('2016-02-01 00:00:00'));
        $object->setDatetimeC(new \DateTime('2016-03-01 00:00:00'));

        $this->assertEquals(
            [
                'datetimeA' => '01.01.2016 00:00:00',
                'datetimeB' => '2016-02-01 00:00:00',
                'datetimeC' => '01 Mar 2016 00:00:00',
            ],
            $this->serializer->serialize($object, DateTimeEntity::class)
        );

        // Object
        $container = new ObjectEntity();
        $container->setDateTime($object);
        $container->setSubObjects([new ObjectEntity(), new ObjectEntity()]);

        $this->assertEquals(
            [
                'dateTime'   => [
                    'datetimeA' => '01.01.2016 00:00:00',
                    'datetimeB' => '2016-02-01 00:00:00',
                    'datetimeC' => '01 Mar 2016 00:00:00',
                ],
                'subObjects' => [
                    [
                        'dateTime'   => null,
                        'subObjects' => [],
                    ],
                    [
                        'dateTime'   => null,
                        'subObjects' => [],
                    ],
                ],
            ],
            $this->serializer->serialize($container, ObjectEntity::class)
        );
    }

    public function testSerializeObjectList()
    {
        // Primitives
        $object = new PrimitiveEntity();
        $list   = [$object, $object, $object];

        $serializedObject = [
            'integer' => null,
            'string'  => null,
            'array'   => [1, '2', true, false],
            'boolean' => true,
            'float'   => 13.37,
        ];

        $this->assertEquals(
            [
                $serializedObject,
                $serializedObject,
                $serializedObject,
            ],
            $this->serializer->serialize($list, PrimitiveEntity::class)
        );
    }

    /**
     * @expectedException \Yamlizer\Exception\NullValueException
     * @expectedExceptionMessage Property propertyName is not nullable
     */
    public function testSerializePropertyNullValueException()
    {
        $propertyMetadata = new PropertyMetadata();
        $propertyMetadata->setPrimitiveType(PropertyMetadata::TYPE_STRING);
        $propertyMetadata->setPropertyName('propertyName');
        $propertyMetadata->setNullable(false);

        $reflectedMethod = new \ReflectionMethod($this->serializer, 'serializeProperty');
        $reflectedMethod->setAccessible(true);
        $reflectedMethod->invokeArgs($this->serializer, [$propertyMetadata, null, new Context()]);
    }

    public function testSerializeArray()
    {
        $context = new Context();

        $propertyMetadata = new PropertyMetadata();
        $propertyMetadata->setPrimitiveType(PropertyMetadata::TYPE_ARRAY);

        $reflectedMethod = new \ReflectionMethod($this->serializer, 'serializeArray');
        $reflectedMethod->setAccessible(true);

        $this->assertEquals(
            [],
            $reflectedMethod->invokeArgs($this->serializer, [$propertyMetadata, [], null, $context])
        );

        $this->assertEquals(
            [],
            $reflectedMethod->invokeArgs($this->serializer, [$propertyMetadata, null, null, $context])
        );

        $array = ['a' => 'apple', 'b' => 'banana', 'numbers' => [1, 2, 3]];
        $this->assertEquals(
            $array,
            $reflectedMethod->invokeArgs($this->serializer, [$propertyMetadata, $array, null, $context])
        );
    }

    /**
     * @expectedException \Yamlizer\Exception\InvalidTypeException
     * @expectedExceptionMessage Expected array instead got object
     */
    public function testSerializeArrayInvalidTypeException()
    {
        $propertyMetadata = new PropertyMetadata();
        $propertyMetadata->setPrimitiveType(PropertyMetadata::TYPE_ARRAY);

        $reflectedMethod = new \ReflectionMethod($this->serializer, 'serializeArray');
        $reflectedMethod->setAccessible(true);
        $reflectedMethod->invokeArgs($this->serializer, [$propertyMetadata, new \stdClass, null, new Context()]);
    }

    /**
     * @expectedException \Yamlizer\Exception\InvalidTypeException
     * @expectedExceptionMessage Expected \DateTime instead got object
     */
    public function testSerializeDateTimeInvalidTypeException()
    {
        $propertyMetadata = new PropertyMetadata();
        $propertyMetadata->setPrimitiveType(PropertyMetadata::TYPE_DATETIME);
        $propertyMetadata->setComplexType('Y-m-d H:i:s');

        $reflectedMethod = new \ReflectionMethod($this->serializer, 'serializeDateTime');
        $reflectedMethod->setAccessible(true);
        $reflectedMethod->invokeArgs($this->serializer, [$propertyMetadata, new \stdClass, new Context()]);
    }

    /**
     * @param string $dateFormat
     * @param string $dateString
     * @param string $expected
     * @dataProvider serializeDateTimeDataProvider
     */
    public function testSerializeDateTime($dateFormat, $dateString, $expected)
    {
        $propertyMetadata = new PropertyMetadata();
        $propertyMetadata->setComplexType($dateFormat);

        $dateTime = $dateString ? new \DateTime($dateString) : null;

        $reflectedMethod = new \ReflectionMethod($this->serializer, 'serializeDateTime');
        $reflectedMethod->setAccessible(true);
        $this->assertEquals(
            $expected,
            $reflectedMethod->invokeArgs($this->serializer, [$propertyMetadata, $dateTime])
        );
    }

    /**
     * @return array
     */
    public function serializeDateTimeDataProvider()
    {
        return [
            [
                'Y-m-d H:i:s',
                null,
                null,
            ],
            [
                'Y-m-d H:i:s',
                '2016-11-22 20:24:48',
                '2016-11-22 20:24:48',
            ],
            [
                'Y-m-d',
                '2016-11-22 20:24:48',
                '2016-11-22',
            ],
            [
                'd. M Y H:i:s',
                '2016-11-22 20:24:48',
                '22. Nov 2016 20:24:48',
            ],
        ];
    }

    /**
     * @expectedException \Yamlizer\Exception\InvalidTypeException
     * @expectedExceptionMessage Expected SomeComplexType instead got DateTime
     */
    public function testSerializeObjectInvalidTypeException()
    {
        $propertyMetadata = new PropertyMetadata();
        $propertyMetadata->setPrimitiveType(PropertyMetadata::TYPE_OBJECT);
        $propertyMetadata->setComplexType('SomeComplexType');

        $reflectedMethod = new \ReflectionMethod($this->serializer, 'serializeObject');
        $reflectedMethod->setAccessible(true);
        $reflectedMethod->invokeArgs($this->serializer, [$propertyMetadata, new \DateTime(), new Context()]);
    }
}
