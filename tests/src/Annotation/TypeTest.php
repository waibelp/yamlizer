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

use Yamlizer\Annotation\Type;
use Yamlizer\Metadata\PropertyMetadata;
use Yamlizer\Tests\Entity\PrimitiveEntity;

/**
 * Class TypeTest
 *
 * @package Yamlizer\Tests\Annotation
 */
class TypeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param string $annotationValue
     * @param string $expectedPrimitive
     * @param string $expectedComplex
     * @dataProvider validAnnotationsDataProvider
     */
    public function testAssignPropertyMetadata($annotationValue, $expectedPrimitive, $expectedComplex)
    {
        $annotation        = new Type();
        $annotation->value = $annotationValue;

        $propertyMetaData = new PropertyMetadata();
        $annotation->assignPropertyMetadata($propertyMetaData, $annotation);
        $this->assertEquals($expectedPrimitive, $propertyMetaData->getPrimitiveType());
        $this->assertEquals($expectedComplex, $propertyMetaData->getComplexType());
    }

    /**
     * @return array
     */
    public function validAnnotationsDataProvider()
    {
        return [
            ['array', PropertyMetadata::TYPE_ARRAY, null],
            ['float', PropertyMetadata::TYPE_FLOAT, null],
            ['integer', PropertyMetadata::TYPE_INTEGER, null],
            ['boolean', PropertyMetadata::TYPE_BOOLEAN, null],
            ['string', PropertyMetadata::TYPE_STRING, null],
            [PrimitiveEntity::class, PropertyMetadata::TYPE_OBJECT, PrimitiveEntity::class],
            ['\\DateTime<Y-m-d H:i:s>', PropertyMetadata::TYPE_DATETIME, 'Y-m-d H:i:s'],
            ['array<' . PrimitiveEntity::class . '>', PropertyMetadata::TYPE_ARRAY, PrimitiveEntity::class],
        ];
    }
}
