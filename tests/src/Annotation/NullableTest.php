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
use Yamlizer\Annotation\Nullable;
use Yamlizer\Exception\InvalidTypeException;
use Yamlizer\Exception\YamlizerException;
use Yamlizer\Metadata\MetadataFactory;
use Yamlizer\Metadata\PropertyMetadata;
use Yamlizer\Serialization\ArrayDeserializer;
use Yamlizer\Serialization\ArraySerializer;
use Yamlizer\Serialization\Context;
use Yamlizer\Tests\Entity\NullableEntity;
use Yamlizer\Tests\Entity\ObjectEntity;

/**
 * Class NullableTest
 *
 * @package Yamlizer\Tests\Annotation
 */
class NullableTest extends \PHPUnit_Framework_TestCase
{
    public function testAssignPropertyMetadata()
    {
        $annotation        = new Nullable();
        $annotation->value = true;

        $propertyMetaData = new PropertyMetadata();
        $annotation->assignPropertyMetadata($propertyMetaData, $annotation);
        $this->assertEquals(true, $propertyMetaData->isNullable());

        $annotation->value = false;
        $annotation->assignPropertyMetadata($propertyMetaData, $annotation);
        $this->assertEquals(false, $propertyMetaData->isNullable());
    }

    public function testSerialization()
    {
        $serializer      = new ArraySerializer(new MetadataFactory(new AnnotationReader()));
        $reflectedMethod = new \ReflectionMethod($serializer, 'serializeProperty');
        $reflectedMethod->setAccessible(true);

        $propertyMetadata = new PropertyMetadata();
        $propertyMetadata->setPrimitiveType(PropertyMetadata::TYPE_STRING);
        $propertyMetadata->setNullable(true);
        $this->assertEquals(
            null,
            $reflectedMethod->invokeArgs($serializer, [$propertyMetadata, null, new Context()]),
            'It should return NULL when nullable is enabled'
        );
    }

    /**
     * @expectedException \Yamlizer\Exception\NullValueException
     * @expectedExceptionMessage Property propertyName is not nullable
     */
    public function testSerializationNullValueException()
    {
        $serializer      = new ArraySerializer(new MetadataFactory(new AnnotationReader()));
        $reflectedMethod = new \ReflectionMethod($serializer, 'serializeProperty');
        $reflectedMethod->setAccessible(true);

        $propertyMetadata = new PropertyMetadata();
        $propertyMetadata->setPropertyName('propertyName');
        $propertyMetadata->setPrimitiveType(PropertyMetadata::TYPE_STRING);
        $propertyMetadata->setNullable(false);
        $reflectedMethod->invokeArgs($serializer, [$propertyMetadata, null, new Context()]);
    }

    /**
     * @expectedException \Yamlizer\Exception\NullValueException
     * @expectedExceptionMessage Property notNullable of Yamlizer\Tests\Entity\NullableEntity is not nullable
     */
    public function testDeserializeNullValueException()
    {
        $metadataFactory = new MetadataFactory(new AnnotationReader());
        $deserializer    = new ArrayDeserializer($metadataFactory);

        $exception = null;
        try {
            $deserializer->deserialize([], NullableEntity::class);
        } catch (YamlizerException $e) {
            $exception = $e;
        }

        if ($exception) {
            $this->assertEquals(NullableEntity::class, $exception->getClass());

            throw $exception;
        }
    }

    /**
     * @expectedException \Yamlizer\Exception\InvalidTypeException
     * @expectedExceptionMessage Expected array got NULL
     */
    public function testDeserializeArrayNullValueException()
    {
        $metadataFactory = new MetadataFactory(new AnnotationReader());
        $deserializer    = new ArrayDeserializer($metadataFactory);

        $classMetadata    = $metadataFactory->classFromObject(ObjectEntity::class);
        $propertyMetadata = $classMetadata->getProperty('subObjects');
        $propertyMetadata->setNullable(false);
        $classMetadata->addProperty($propertyMetadata);

        $reflectedMethod = new \ReflectionMethod($deserializer, 'deserializeArray');
        $reflectedMethod->setAccessible(true);

        $exception = null;
        try {
            $reflectedMethod->invokeArgs($deserializer, [null, $propertyMetadata, new Context()]);
        } catch (InvalidTypeException $e) {
            $exception = $e;
        }

        if ($exception) {
            $this->assertEquals('subObjects', $exception->getKey());
            $this->assertEquals('array', $exception->getExpectedType());
            $this->assertEquals('NULL', $exception->getActualType());

            throw $exception;
        }
    }

    public function testDeserializeArrayNullValueAllowed()
    {
        $metadataFactory = new MetadataFactory(new AnnotationReader());
        $deserializer    = new ArrayDeserializer($metadataFactory);

        $classMetadata    = $metadataFactory->classFromObject(ObjectEntity::class);
        $propertyMetadata = $classMetadata->getProperty('subObjects');
        $propertyMetadata->setNullable(true);
        $classMetadata->addProperty($propertyMetadata);

        $reflectedMethod = new \ReflectionMethod($deserializer, 'deserializeArray');
        $reflectedMethod->setAccessible(true);
        $this->assertNull($reflectedMethod->invokeArgs($deserializer, [null, $propertyMetadata, new Context()]));
    }

    /**
     * @expectedException \Yamlizer\Exception\InvalidTypeException
     * @expectedExceptionMessage Expected Yamlizer\Tests\Entity\ObjectEntity got NULL
     */
    public function testDeserializeObjectNullValueException()
    {
        $metadataFactory = new MetadataFactory(new AnnotationReader());
        $deserializer    = new ArrayDeserializer($metadataFactory);

        $classMetadata    = $metadataFactory->classFromObject(ObjectEntity::class);
        $propertyMetadata = $classMetadata->getProperty('subObjects');
        $propertyMetadata->setNullable(false);
        $classMetadata->addProperty($propertyMetadata);

        $reflectedMethod = new \ReflectionMethod($deserializer, 'deserializeObject');
        $reflectedMethod->setAccessible(true);
        $reflectedMethod->invokeArgs($deserializer, [null, $propertyMetadata, new Context()]);
    }

    public function testDeserializeObjectNullValueAllowed()
    {
        $metadataFactory = new MetadataFactory(new AnnotationReader());
        $deserializer    = new ArrayDeserializer($metadataFactory);

        $classMetadata    = $metadataFactory->classFromObject(ObjectEntity::class);
        $propertyMetadata = $classMetadata->getProperty('subObjects');
        $propertyMetadata->setNullable(true);
        $classMetadata->addProperty($propertyMetadata);

        $reflectedMethod = new \ReflectionMethod($deserializer, 'deserializeObject');
        $reflectedMethod->setAccessible(true);
        $this->assertNull($reflectedMethod->invokeArgs($deserializer, [null, $propertyMetadata, new Context()]));
    }

    /**
     * @expectedException \Yamlizer\Exception\InvalidTypeException
     * @expectedExceptionMessage Expected string got NULL
     */
    public function testDeserializeDateTimeNullValueException()
    {
        $metadataFactory = new MetadataFactory(new AnnotationReader());
        $deserializer    = new ArrayDeserializer($metadataFactory);

        $classMetadata    = $metadataFactory->classFromObject(ObjectEntity::class);
        $propertyMetadata = $classMetadata->getProperty('subObjects');
        $propertyMetadata->setNullable(false);
        $classMetadata->addProperty($propertyMetadata);

        $reflectedMethod = new \ReflectionMethod($deserializer, 'deserializeDateTime');
        $reflectedMethod->setAccessible(true);
        $reflectedMethod->invokeArgs($deserializer, [null, $propertyMetadata, new Context()]);
    }

    public function testDeserializeDateTimeNullValueAllowed()
    {
        $metadataFactory = new MetadataFactory(new AnnotationReader());
        $deserializer    = new ArrayDeserializer($metadataFactory);

        $classMetadata    = $metadataFactory->classFromObject(ObjectEntity::class);
        $propertyMetadata = $classMetadata->getProperty('subObjects');
        $propertyMetadata->setNullable(true);
        $classMetadata->addProperty($propertyMetadata);

        $reflectedMethod = new \ReflectionMethod($deserializer, 'deserializeDateTime');
        $reflectedMethod->setAccessible(true);
        $this->assertNull($reflectedMethod->invokeArgs($deserializer, [null, $propertyMetadata, new Context()]));
    }
}
