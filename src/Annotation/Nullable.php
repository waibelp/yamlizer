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
 * Class Nullable
 *
 * @Annotation
 * @Target("PROPERTY")
 * @package Yamlizer\Annotation
 */
class Nullable extends YamlizerAnnotation
{
    /**
     * @param PropertyMetadata   $propertyMetadata
     * @param YamlizerAnnotation $annotation
     */
    public function assignPropertyMetadata(PropertyMetadata $propertyMetadata, YamlizerAnnotation $annotation)
    {
        $propertyMetadata->setNullable(filter_var($annotation->value, FILTER_VALIDATE_BOOLEAN));
    }
}
