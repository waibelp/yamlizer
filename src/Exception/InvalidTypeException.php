<?php
/**
 * This file is part of the Yamlizer package.
 *
 * (c) Pierre Waibel <waibelp85@googlemail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yamlizer\Exception;

/**
 * Class InvalidTypeException
 *
 * @package Yamlizer\Exception
 */
class InvalidTypeException extends YamlizerException
{
    /**
     * @var string
     */
    protected $expectedType;

    /**
     * @var string
     */
    protected $actualType;

    /**
     * @return string
     */
    public function getExpectedType()
    {
        return $this->expectedType;
    }

    /**
     * @param string $expectedType
     */
    public function setExpectedType($expectedType)
    {
        $this->expectedType = $expectedType;
    }

    /**
     * @return string
     */
    public function getActualType()
    {
        return $this->actualType;
    }

    /**
     * @param string $actualType
     */
    public function setActualType($actualType)
    {
        $this->actualType = $actualType;
    }
}
