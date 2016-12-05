<?php
/**
 * This file is part of the Yamlizer package.
 *
 * (c) Pierre Waibel <waibelp85@googlemail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yamlizer\Annotation;

use Yamlizer\Metadata\PropertyMetadata;

/**
 * Class Type
 *
 * @Annotation
 * @Target("PROPERTY")
 * @package Yamlizer\Annotation
 */
class Type extends YamlizerAnnotation
{
    /**
     * @param PropertyMetadata   $propertyMetadata
     * @param YamlizerAnnotation $annotation
     */
    public function assignPropertyMetadata(PropertyMetadata $propertyMetadata, YamlizerAnnotation $annotation)
    {
        // Primitive and complex type
        $primitives = [
            PropertyMetadata::TYPE_ARRAY,
            PropertyMetadata::TYPE_BOOLEAN,
            PropertyMetadata::TYPE_FLOAT,
            PropertyMetadata::TYPE_INTEGER,
            PropertyMetadata::TYPE_STRING,
        ];

        if (preg_match('/^array<(.*)>$/', $annotation->value, $matches)) {
            // Complex array
            $propertyMetadata->setPrimitiveType(PropertyMetadata::TYPE_ARRAY);
            $propertyMetadata->setComplexType($matches[1]);
        } elseif (in_array($annotation->value, $primitives)) {
            // Primitive: array, boolean, float, integer, string
            $propertyMetadata->setPrimitiveType($annotation->value);
        } elseif (preg_match('/^\\\\DateTime<([^>]*)>?$/', $annotation->value, $matches)) {
            // \DateTime
            $propertyMetadata->setPrimitiveType(PropertyMetadata::TYPE_DATETIME);
            $propertyMetadata->setComplexType($matches[1]);
        } else {
            // Object
            $propertyMetadata->setPrimitiveType(PropertyMetadata::TYPE_OBJECT);
            $propertyMetadata->setComplexType($annotation->value);
        }

        // Setter and getter method names
        $propertyMetadata->setSetterName(sprintf('set%s', ucfirst($propertyMetadata->getPropertyName())));
        if ('boolean' === $propertyMetadata->getPrimitiveType()) {
            $propertyMetadata->setGetterName(sprintf('is%s', ucfirst($propertyMetadata->getPropertyName())));
        } else {
            $propertyMetadata->setGetterName(sprintf('get%s', ucfirst($propertyMetadata->getPropertyName())));
        }
    }
}
