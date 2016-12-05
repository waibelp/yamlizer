<?php
/**
 * This file is part of the Yamlizer package.
 *
 * (c) Pierre Waibel <waibelp85@googlemail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yamlizer\Cache;

use Yamlizer\Metadata\ClassMetadata;

/**
 * Interface ClassMetadataCacheInterface
 *
 * @package Yamlizer\Cache
 */
interface ClassMetadataCacheInterface
{
    /**
     * @param string $class
     * @return ClassMetadata|null
     */
    public function read($class);

    /**
     * @param ClassMetadata $classMetadata
     */
    public function write(ClassMetadata $classMetadata);
}
