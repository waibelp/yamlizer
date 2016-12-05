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

use Yamlizer\Annotation\SetterName;
use Yamlizer\Metadata\PropertyMetadata;
use Yamlizer\Tests\Entity\PrimitiveEntity;

/**
 * Class SetterNameTest
 *
 * @package Yamlizer\Tests\Annotation
 */
class SetterNameTest extends \PHPUnit_Framework_TestCase
{
    public function testAssignPropertyMetadata()
    {
        $annotation        = new SetterName();
        $annotation->value = 'someSettersMethodName';

        $propertyMetaData = new PropertyMetadata();
        $annotation->assignPropertyMetadata($propertyMetaData, $annotation);
        $this->assertEquals('someSettersMethodName', $propertyMetaData->getSetterName());
    }

    /**
     * @expectedException \Yamlizer\Exception\YamlizerException
     * @expectedExceptionMessage [SetterName] Method unknownSetterMethod of class Yamlizer\Tests\Entity\PrimitiveEntity does not exist!
     */
    public function testValidatePropertyMetadataInvalid()
    {
        $type             = new SetterName();
        $object           = new PrimitiveEntity();
        $propertyMetadata = new PropertyMetadata();
        $propertyMetadata->setSetterName('unknownSetterMethod');

        $reflectedClass = new \ReflectionClass($object);
        $type->validatePropertyMetadata($propertyMetadata, $reflectedClass);
    }
}
