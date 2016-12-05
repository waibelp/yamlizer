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
 * Class Groups
 *
 * @Annotation
 * @Target("PROPERTY")
 * @package Yamlizer\Annotation
 */
class Groups extends YamlizerAnnotation
{
    /**
     * @param PropertyMetadata   $propertyMetadata
     * @param YamlizerAnnotation $annotation
     */
    public function assignPropertyMetadata(PropertyMetadata $propertyMetadata, YamlizerAnnotation $annotation)
    {
        $groups = [];
        if (false !== strpos($annotation->value, ',')) {
            foreach (explode(',', $annotation->value) as $group) {
                $group = trim($group);
                if (!empty($group)) {
                    $groups[] = $group;
                }
            }
        } else {
            $groups = [trim($annotation->value)];
        }

        $propertyMetadata->setGroups($groups);
    }
}
