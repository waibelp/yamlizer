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

use Yamlizer\Exception\YamlizerException;
use Yamlizer\Metadata\PropertyMetadata;

/**
 * Class GetterName
 *
 * @Annotation
 * @Target("PROPERTY")
 * @package Yamlizer\Annotation
 */
class GetterName extends YamlizerAnnotation
{
    /**
     * @param PropertyMetadata   $propertyMetadata
     * @param YamlizerAnnotation $annotation
     */
    public function assignPropertyMetadata(PropertyMetadata $propertyMetadata, YamlizerAnnotation $annotation)
    {
        $propertyMetadata->setGetterName($annotation->value);
    }

    /**
     * @param PropertyMetadata $propertyMetadata
     * @param \ReflectionClass $reflectedClass
     * @throws YamlizerException
     */
    public function validatePropertyMetadata(PropertyMetadata $propertyMetadata, \ReflectionClass $reflectedClass)
    {
        try {
            $reflectedClass->getMethod($propertyMetadata->getGetterName());
        } catch (\ReflectionException $e) {
            throw new YamlizerException(
                sprintf(
                    '[GetterName] Method %s of class %s does not exist!',
                    $propertyMetadata->getGetterName(),
                    $reflectedClass->getName()
                ),
                0,
                $e
            );
        }
    }
}
