<?php
/**
 * This file is part of the Yamlizer package.
 *
 * (c) Pierre Waibel <waibelp85@googlemail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yamlizer\Serialization;

/**
 * Class Context
 *
 * @package Yamlizer\Serialization
 */
class Context
{
    /**
     * Class group map
     *
     * @var array
     */
    protected $groups = [];

    /**
     * Get groups for given class
     *
     * @param string $class Fully qualified class name
     * @return array
     */
    public function getGroups($class)
    {
        return isset($this->groups[$class]) ? $this->groups[$class] : [];
    }

    /**
     * Set groups for given class
     *
     * @param string $class  Fully qualified class name
     * @param array  $groups Name of groups
     */
    public function setGroups($class, array $groups)
    {
        $this->groups[$class] = $groups;
    }
}
