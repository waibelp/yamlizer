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
use Yamlizer\Annotation\Readonly;
use Yamlizer\Metadata\MetadataFactory;
use Yamlizer\Metadata\PropertyMetadata;
use Yamlizer\Serialization\ArrayDeserializer;
use Yamlizer\Tests\Entity\ReadonlyEntity;

/**
 * Class ReadonlyTest
 *
 * @package Yamlizer\Tests\Annotation
 */
class ReadonlyTest extends \PHPUnit_Framework_TestCase
{
    public function testAssignPropertyMetadata()
    {
        $annotation        = new Readonly();
        $annotation->value = true;

        $propertyMetaData = new PropertyMetadata();
        $annotation->assignPropertyMetadata($propertyMetaData, $annotation);
        $this->assertEquals(true, $propertyMetaData->isReadonly());

        $annotation->value = false;
        $annotation->assignPropertyMetadata($propertyMetaData, $annotation);
        $this->assertEquals(false, $propertyMetaData->isReadonly());
    }

    public function testDeserialize()
    {
        $metadataFactory = new MetadataFactory(new AnnotationReader());
        $deserializer    = new ArrayDeserializer($metadataFactory);

        $object = new ReadonlyEntity();
        $input  = [
            'readable'  => 'String will not be overwritten',
            'writeable' => 'String was overwritten',
        ];

        /** @var ReadonlyEntity $object */
        $object = $deserializer->deserialize($input, ReadonlyEntity::class, $object);
        $this->assertEquals('readonly', $object->getReadable());
        $this->assertEquals('String was overwritten', $object->getWriteable());
    }
}
