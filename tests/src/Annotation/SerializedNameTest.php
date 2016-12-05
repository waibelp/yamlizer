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
use Yamlizer\Annotation\SerializedName;
use Yamlizer\Metadata\MetadataFactory;
use Yamlizer\Metadata\PropertyMetadata;
use Yamlizer\Serialization\ArrayDeserializer;
use Yamlizer\Serialization\ArraySerializer;
use Yamlizer\Tests\Entity\SerializedNameEntity;

/**
 * Class SerializedNameTest
 *
 * @package Yamlizer\Tests\Annotation
 */
class SerializedNameTest extends \PHPUnit_Framework_TestCase
{
    public function testAssignPropertyMetadata()
    {
        $annotation        = new SerializedName();
        $annotation->value = 'serializedNameOfProperty';

        $propertyMetaData = new PropertyMetadata();
        $annotation->assignPropertyMetadata($propertyMetaData, $annotation);
        $this->assertEquals('serializedNameOfProperty', $propertyMetaData->getSerializedName());
    }

    public function testSerialize()
    {
        $metadataFactory = new MetadataFactory(new AnnotationReader());
        $serializer      = new ArraySerializer($metadataFactory);

        $object = new SerializedNameEntity();
        $output = $serializer->serialize($object, SerializedNameEntity::class);
        $this->assertArrayHasKey('otherName', $output);
        $this->assertEquals($object->getSomeName(), $output['otherName']);
    }

    public function testDeserialize()
    {
        $metadataFactory = new MetadataFactory(new AnnotationReader());
        $deserializer    = new ArrayDeserializer($metadataFactory);

        /** @var SerializedNameEntity $object */
        $object = $deserializer->deserialize(['otherName' => 'some other content'], SerializedNameEntity::class);
        $this->assertEquals('some other content', $object->getSomeName());
    }
}
