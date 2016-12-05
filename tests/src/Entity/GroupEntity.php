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

use Yamlizer\Annotation\Groups;
use Yamlizer\Annotation\Type;

/**
 * Class GroupEntity
 *
 * @package Yamlizer\Tests\Entity
 */
class GroupEntity
{
    /**
     * @Type("integer")
     * @Groups("odd, all")
     * @var int
     */
    protected $one = 1;

    /**
     * @Type("integer")
     * @Groups("even, all")
     * @var int
     */
    protected $two = 2;

    /**
     * @Type("integer")
     * @Groups("odd, all")
     * @var int
     */
    protected $three = 3;

    /**
     * @Type("integer")
     * @Groups("even, all")
     * @var int
     */
    protected $four = 4;

    /**
     * @return int
     */
    public function getOne()
    {
        return $this->one;
    }

    /**
     * @param int $one
     * @return $this
     */
    public function setOne($one)
    {
        $this->one = $one;

        return $this;
    }

    /**
     * @return int
     */
    public function getTwo()
    {
        return $this->two;
    }

    /**
     * @param int $two
     * @return $this
     */
    public function setTwo($two)
    {
        $this->two = $two;

        return $this;
    }

    /**
     * @return int
     */
    public function getThree()
    {
        return $this->three;
    }

    /**
     * @param int $three
     * @return $this
     */
    public function setThree($three)
    {
        $this->three = $three;

        return $this;
    }

    /**
     * @return int
     */
    public function getFour()
    {
        return $this->four;
    }

    /**
     * @param int $four
     * @return $this
     */
    public function setFour($four)
    {
        $this->four = $four;

        return $this;
    }
}
