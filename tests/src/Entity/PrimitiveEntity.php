<?php
/**
 * This file is part of the Yamlizer package.
 *
 * (c) Pierre Waibel <waibelp85@googlemail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yamlizer\Tests\Entity;

use Yamlizer\Annotation\Type;

/**
 * Class PrimitiveEntity
 *
 * @package Yamlizer\Tests\Entity
 */
class PrimitiveEntity
{
    /**
     * @Type("integer")
     * @var int
     */
    protected $integer;

    /**
     * @Type("string")
     * @var string
     */
    protected $string;

    /**
     * @Type("array")
     * @var array
     */
    protected $array = [
        1,
        '2',
        true,
        false,
    ];

    /**
     * @Type("boolean")
     * @var bool
     */
    protected $boolean = true;

    /**
     * @Type("float")
     * @var float
     */
    protected $float = 13.37;

    /**
     * @return int
     */
    public function getInteger()
    {
        return $this->integer;
    }

    /**
     * @param int $integer
     */
    public function setInteger($integer)
    {
        $this->integer = $integer;
    }

    /**
     * @return string
     */
    public function getString()
    {
        return $this->string;
    }

    /**
     * @param string $string
     */
    public function setString($string)
    {
        $this->string = $string;
    }

    /**
     * @return array
     */
    public function getArray()
    {
        return $this->array;
    }

    /**
     * @param array $array
     */
    public function setArray($array)
    {
        $this->array = $array;
    }

    /**
     * @return boolean
     */
    public function isBoolean()
    {
        return $this->boolean;
    }

    /**
     * @param boolean $boolean
     */
    public function setBoolean($boolean)
    {
        $this->boolean = $boolean;
    }

    /**
     * @return float
     */
    public function getFloat()
    {
        return $this->float;
    }

    /**
     * @param float $float
     */
    public function setFloat($float)
    {
        $this->float = $float;
    }
}
