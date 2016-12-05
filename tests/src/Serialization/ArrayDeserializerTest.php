<?php
/**
 * This file is part of the Yamlizer package.
 *
 * (c) Pierre Waibel <waibelp85@googlemail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yamlizer\Tests\Deserialization;

use Doctrine\Common\Annotations\AnnotationReader;
use Yamlizer\Metadata\MetadataFactory;
use Yamlizer\Serialization\ArrayDeserializer;
use Yamlizer\Serialization\Context;
use Yamlizer\Tests\Entity\DateTimeEntity;
use Yamlizer\Tests\Entity\ObjectEntity;
use Yamlizer\Tests\Entity\PrimitiveEntity;

/**
 * Class ArrayDeserializerTest
 *
 * @package Yamlizer\Tests\Deserialization
 */
class ArrayDeserializerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ArrayDeserializer
     */
    protected $deserializer;

    /**
     * @var MetadataFactory
     */
    protected $metadataFactory;

    protected function setUp()
    {
        parent::setUp();

        $this->metadataFactory = new MetadataFactory(new AnnotationReader());
        $this->deserializer    = new ArrayDeserializer($this->metadataFactory);
        $this->deserializer->setDefaultContext(new Context());
    }

    protected function tearDown()
    {
        $this->deserializer    = null;
        $this->metadataFactory = null;

        parent::tearDown();
    }

    public function testDeserialize()
    {
        // Primitive
        $array = [
            'integer' => 12,
            'float'   => 3.14,
            'array'   => [1, 2, 3],
            'boolean' => false,
        ];

        $expectedObject = new PrimitiveEntity();
        $expectedObject->setInteger(12);
        $expectedObject->setFloat(3.14);
        $expectedObject->setArray([1, 2, 3]);
        $expectedObject->setBoolean(false);

        $actualObject = $this->deserializer->deserialize($array, PrimitiveEntity::class);
        $this->assertEquals($expectedObject, $actualObject);

        // \DateTime
        $array = [
            'datetimeA' => '01.01.2016 00:00:00',
            'datetimeB' => '2016-02-01 00:00:00',
            'datetimeC' => '01 Mar 2016 00:00:00',
        ];

        $expectedObject = new DateTimeEntity();
        $expectedObject->setDatetimeA(new \DateTime('2016-01-01 00:00:00'));
        $expectedObject->setDatetimeB(new \DateTime('2016-02-01 00:00:00'));
        $expectedObject->setDatetimeC(new \DateTime('2016-03-01 00:00:00'));

        $actualObject = $this->deserializer->deserialize($array, DateTimeEntity::class);
        $this->assertEquals($expectedObject, $actualObject);

        // array<Object>
        $array = [
            'dateTime'   => [
                'datetimeA' => '',
                'datetimeB' => '',
                'datetimeC' => '',
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
        ];

        $expectedObject = new ObjectEntity();
        $expectedObject->setDateTime(new DateTimeEntity());
        $expectedObject->setSubObjects([new ObjectEntity(), new ObjectEntity()]);

        $actualObject = $this->deserializer->deserialize($array, ObjectEntity::class);
        $this->assertEquals($expectedObject, $actualObject);
    }

    public function testDeserializeObjectList()
    {
        // Primitive
        $array = [
            'integer' => 12,
            'float'   => 3.14,
            'array'   => [1, 2, 3],
            'boolean' => false,
        ];

        $expectedObject = new PrimitiveEntity();
        $expectedObject->setInteger(12);
        $expectedObject->setFloat(3.14);
        $expectedObject->setArray([1, 2, 3]);
        $expectedObject->setBoolean(false);

        $objectList = $this->deserializer->deserialize(
            [$array, $array, $array],
            sprintf('array<%s>', PrimitiveEntity::class)
        );

        $this->assertCount(3, $objectList);
        $this->assertEquals($expectedObject, $objectList[0]);
        $this->assertEquals($expectedObject, $objectList[1]);
        $this->assertEquals($expectedObject, $objectList[2]);
    }
}
