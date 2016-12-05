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
 * Class InMemoryCache
 *
 * @package Yamlizer\Cache
 */
class InMemoryCache implements ClassMetadataCacheInterface
{
    /**
     * @var ClassMetadata
     */
    protected $cache = [];

    /**
     * @param string $class
     * @return ClassMetadata|null
     */
    public function read($class)
    {
        return isset($this->cache[$class]) ? $this->cache[$class] : null;
    }

    /**
     * @param ClassMetadata $classMetadata
     */
    public function write(ClassMetadata $classMetadata)
    {
        $this->cache[$classMetadata->getClass()] = $classMetadata;
    }
}
