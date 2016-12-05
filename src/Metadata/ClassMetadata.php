<?php
/**
 * This file is part of the Yamlizer package.
 *
 * (c) Pierre Waibel <waibelp85@googlemail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yamlizer\Metadata;

/**
 * Class ClassMetadata
 *
 * @package Yamlizer\Metadata
 */
class ClassMetadata
{
    /**
     * @var string
     */
    protected $class;

    /**
     * @var PropertyMetadata[]
     */
    protected $properties = [];

    /**
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * @param string $class
     */
    public function setClass($class)
    {
        $this->class = $class;
    }

    /**
     * @return PropertyMetadata[]
     */
    public function getProperties()
    {
        return $this->properties;
    }

    /**
     * @param string $propertyName
     * @return PropertyMetadata|null
     */
    public function getProperty($propertyName)
    {
        return isset($this->properties[$propertyName]) ? $this->properties[$propertyName] : null;
    }

    /**
     * @param PropertyMetadata $property
     */
    public function addProperty(PropertyMetadata $property)
    {
        $name = $property->getPropertyName();

        $this->properties[$name] = $property;
    }
}
