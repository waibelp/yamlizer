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
use Yamlizer\Annotation\PreserveKeys;
use Yamlizer\Metadata\MetadataFactory;
use Yamlizer\Metadata\PropertyMetadata;
use Yamlizer\Serialization\ArraySerializer;
use Yamlizer\Serialization\Context;

/**
 * Class PreserveKeysTest
 *
 * @package Yamlizer\Tests\Annotation
 */
class PreserveKeysTest extends \PHPUnit_Framework_TestCase
{
    public function testAssignPropertyMetadata()
    {
        $annotation        = new PreserveKeys();
        $annotation->value = true;

        $propertyMetaData = new PropertyMetadata();
        $annotation->assignPropertyMetadata($propertyMetaData, $annotation);
        $this->assertEquals(true, $propertyMetaData->isPreserveKeys());
    }

    public function testSerialization()
    {
        $serializer      = new ArraySerializer(new MetadataFactory(new AnnotationReader()));
        $reflectedMethod = new \ReflectionMethod($serializer, 'serializeProperty');
        $reflectedMethod->setAccessible(true);

        $propertyMetadata = new PropertyMetadata();

        $value = [
            'a' => 'apple',
            'b' => 'blueberry',
            'c' => 'coconut',
        ];

        $propertyMetadata->setPrimitiveType(PropertyMetadata::TYPE_ARRAY);
        $propertyMetadata->setPreserveKeys(false);
        $this->assertEquals(
            [
                'apple',
                'blueberry',
                'coconut'
            ],
            $reflectedMethod->invokeArgs($serializer, [$propertyMetadata, $value, new Context()]),
            'Array element keys should not be serialized with PreserveKeys disabled'
        );

        $propertyMetadata->setPrimitiveType(PropertyMetadata::TYPE_ARRAY);
        $propertyMetadata->setPreserveKeys(true);
        $this->assertEquals(
            $value,
            $reflectedMethod->invokeArgs($serializer, [$propertyMetadata, $value, new Context()]),
            'Array element keys must be serialized with PreserveKeys enabled'
        );
    }
}
