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
 * Class PropertyMetadata
 *
 * @package Yamlizer\Metadata
 */
class PropertyMetadata
{
    const TYPE_STRING   = 'string';
    const TYPE_BOOLEAN  = 'boolean';
    const TYPE_INTEGER  = 'integer';
    const TYPE_FLOAT    = 'float';
    const TYPE_ARRAY    = 'array';
    const TYPE_OBJECT   = 'object';
    const TYPE_DATETIME = 'datetime';

    /**
     * @var string
     */
    protected $propertyName;

    /**
     * @var string
     */
    protected $serializedName;

    /**
     * @var string
     */
    protected $getterName;

    /**
     * @var string
     */
    protected $setterName;

    /**
     * @var string
     */
    protected $primitiveType;

    /**
     * @var string
     */
    protected $complexType;

    /**
     * @var array
     */
    protected $groups = [];

    /**
     * @var bool
     */
    protected $nullable = true;

    /**
     * @var bool
     */
    protected $readonly = false;

    /**
     * @var bool
     */
    protected $preserveKeys = true;

    /**
     * @return string
     */
    public function getPropertyName()
    {
        return $this->propertyName;
    }

    /**
     * @param string $propertyName
     */
    public function setPropertyName($propertyName)
    {
        $this->propertyName = $propertyName;
    }

    /**
     * @return string
     */
    public function getSerializedName()
    {
        return $this->serializedName;
    }

    /**
     * @param string $serializedName
     */
    public function setSerializedName($serializedName)
    {
        $this->serializedName = $serializedName;
    }

    /**
     * @return string
     */
    public function getGetterName()
    {
        return $this->getterName;
    }

    /**
     * @param string $getterName
     */
    public function setGetterName($getterName)
    {
        $this->getterName = $getterName;
    }

    /**
     * @return string
     */
    public function getSetterName()
    {
        return $this->setterName;
    }

    /**
     * @param string $setterName
     */
    public function setSetterName($setterName)
    {
        $this->setterName = $setterName;
    }

    /**
     * @return string
     */
    public function getPrimitiveType()
    {
        return $this->primitiveType;
    }

    /**
     * @param string $primitiveType
     */
    public function setPrimitiveType($primitiveType)
    {
        $this->primitiveType = $primitiveType;
    }

    /**
     * @return string
     */
    public function getComplexType()
    {
        return $this->complexType;
    }

    /**
     * @param string $complexType
     */
    public function setComplexType($complexType)
    {
        $this->complexType = $complexType;
    }

    /**
     * @return array
     */
    public function getGroups()
    {
        return $this->groups;
    }

    /**
     * @param array $groups
     */
    public function setGroups($groups)
    {
        $this->groups = $groups;
    }

    /**
     * @return boolean
     */
    public function isNullable()
    {
        return $this->nullable;
    }

    /**
     * @param boolean $nullable
     */
    public function setNullable($nullable)
    {
        $this->nullable = $nullable;
    }

    /**
     * @return boolean
     */
    public function isReadonly()
    {
        return $this->readonly;
    }

    /**
     * @param boolean $readonly
     */
    public function setReadonly($readonly)
    {
        $this->readonly = $readonly;
    }

    /**
     * @return boolean
     */
    public function isPreserveKeys()
    {
        return $this->preserveKeys;
    }

    /**
     * @param boolean $preserveKeys
     */
    public function setPreserveKeys($preserveKeys)
    {
        $this->preserveKeys = $preserveKeys;
    }

    /**
     * Whether this property is in one of the given groups or not
     *
     * @param array $groups
     * @return bool
     */
    public function isPropertyInGroup(array $groups)
    {
        foreach ($this->groups as $group) {
            if (in_array($group, $groups)) {
                return true;
            }
        }

        return false;
    }
}
