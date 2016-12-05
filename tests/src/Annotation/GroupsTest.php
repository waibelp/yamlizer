<?php
/**
 * This file is part of the Yamlizer package.
 *
 * (c) Pierre Waibel <waibelp85@googlemail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yamlizer\Tests\Annotation;

use Doctrine\Common\Annotations\AnnotationReader;
use Yamlizer\Annotation\Groups;
use Yamlizer\Metadata\MetadataFactory;
use Yamlizer\Metadata\PropertyMetadata;
use Yamlizer\Serialization\ArrayDeserializer;
use Yamlizer\Serialization\ArraySerializer;
use Yamlizer\Serialization\Context;
use Yamlizer\Tests\Entity\GroupEntity;
use Yamlizer\Tests\Entity\ObjectEntity;

/**
 * Class GroupsTest
 *
 * @package Yamlizer\Tests\Annotation
 */
class GroupsTest extends \PHPUnit_Framework_TestCase
{
    public function testAssignPropertyMetadataSingleGroup()
    {
        $annotation        = new Groups();
        $annotation->value = 'two';

        $propertyMetaData = new PropertyMetadata();
        $annotation->assignPropertyMetadata($propertyMetaData, $annotation);
        $this->assertEquals(['two'], $propertyMetaData->getGroups());
    }

    public function testAssignPropertyMetadataMultipleGroups()
    {
        $annotation        = new Groups();
        $annotation->value = ' one, two, three';

        $propertyMetaData = new PropertyMetadata();
        $annotation->assignPropertyMetadata($propertyMetaData, $annotation);
        $this->assertEquals(['one', 'two', 'three'], $propertyMetaData->getGroups());
    }

    public function testSerialize()
    {
        $metadataFactory = new MetadataFactory(new AnnotationReader());
        $serializer      = new ArraySerializer($metadataFactory);

        $groupEntity = new GroupEntity();

        $context = new Context();
        $this->assertEquals(
            [
                'one'   => 1,
                'two'   => 2,
                'three' => 3,
                'four'  => 4,
            ],
            $serializer->serialize($groupEntity, GroupEntity::class, $context)
        );

        $context->setGroups(GroupEntity::class, ['all']);
        $this->assertEquals(
            [
                'one'   => 1,
                'two'   => 2,
                'three' => 3,
                'four'  => 4,
            ],
            $serializer->serialize($groupEntity, GroupEntity::class, $context)
        );

        $context->setGroups(GroupEntity::class, ['even']);
        $this->assertEquals(
            [
                'two'  => 2,
                'four' => 4,
            ],
            $serializer->serialize($groupEntity, GroupEntity::class, $context)
        );

        $context->setGroups(GroupEntity::class, ['odd']);
        $this->assertEquals(
            [
                'one'   => 1,
                'three' => 3,
            ],
            $serializer->serialize($groupEntity, GroupEntity::class, $context)
        );
    }

    public function testSerializeObjectList()
    {
        $metadataFactory = new MetadataFactory(new AnnotationReader());
        $serializer      = new ArraySerializer($metadataFactory);

        $context = new Context();
        $context->setGroups(ObjectEntity::class, ['three']);

        $reflectedMethod = new \ReflectionMethod($serializer, 'serializeArray');
        $reflectedMethod->setAccessible(true);

        $value = [
            new ObjectEntity(),
            new ObjectEntity(),
        ];

        $metaData = $metadataFactory->classFromObject(new ObjectEntity());
        $this->assertEquals(
            [],
            $reflectedMethod->invokeArgs(
                $serializer,
                [$metaData->getProperty('subObjects'), $value, ObjectEntity::class, $context]
            )
        );
    }

    public function testDeserialize()
    {
        $context    = new Context();
        $inputArray = [
            'one'   => 8,
            'two'   => 8,
            'three' => 8,
            'four'  => 8,
        ];

        $metadataFactory = new MetadataFactory(new AnnotationReader());
        $deserializer    = new ArrayDeserializer($metadataFactory);

        // No group given
        $object         = new GroupEntity();
        $expectedObject = new GroupEntity();
        $expectedObject->setOne(8)->setTwo(8)->setThree(8)->setFour(8);

        $this->assertEquals(
            $expectedObject,
            $deserializer->deserialize(
                $inputArray,
                GroupEntity::class,
                $object,
                $context
            )
        );

        // 'all' group given
        $object         = new GroupEntity();
        $expectedObject = new GroupEntity();
        $expectedObject->setOne(8)->setTwo(8)->setThree(8)->setFour(8);

        $context->setGroups(GroupEntity::class, ['all']);
        $this->assertEquals(
            $expectedObject,
            $deserializer->deserialize(
                $inputArray,
                GroupEntity::class,
                $object,
                $context
            )
        );

        // 'even' group given
        $object         = new GroupEntity();
        $expectedObject = new GroupEntity();
        $expectedObject->setOne(1)->setTwo(8)->setThree(3)->setFour(8);

        $context->setGroups(GroupEntity::class, ['even']);
        $this->assertEquals(
            $expectedObject,
            $deserializer->deserialize(
                $inputArray,
                GroupEntity::class,
                $object,
                $context
            )
        );

        // 'odd' group given
        $object         = new GroupEntity();
        $expectedObject = new GroupEntity();
        $expectedObject->setOne(8)->setTwo(2)->setThree(8)->setFour(4);

        $context->setGroups(GroupEntity::class, ['odd']);
        $this->assertEquals(
            $expectedObject,
            $deserializer->deserialize(
                $inputArray,
                GroupEntity::class,
                $object,
                $context
            )
        );
    }
}
