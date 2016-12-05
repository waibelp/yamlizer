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
use Yamlizer\Annotation\Type as Type;

/**
 * Class DateTimeEntity
 *
 * @package Yamlizer\Tests\Entity
 */
class DateTimeEntity
{
    /**
     * @Type("\DateTime<d.m.Y H:i:s>")
     * @Groups("one")
     * @var \DateTime
     */
    protected $datetimeA;

    /**
     * @Type("\DateTime<Y-m-d H:i:s>")
     * @Groups("two")
     * @var \DateTime
     */
    protected $datetimeB;

    /**
     * @Type("\DateTime<d M Y H:i:s>")
     * @Groups("three")
     * @var \DateTime
     */
    protected $datetimeC;

    /**
     * @return \DateTime
     */
    public function getDatetimeA()
    {
        return $this->datetimeA;
    }

    /**
     * @param \DateTime $datetimeA
     */
    public function setDatetimeA($datetimeA)
    {
        $this->datetimeA = $datetimeA;
    }

    /**
     * @return \DateTime
     */
    public function getDatetimeB()
    {
        return $this->datetimeB;
    }

    /**
     * @param \DateTime $datetimeB
     */
    public function setDatetimeB($datetimeB)
    {
        $this->datetimeB = $datetimeB;
    }

    /**
     * @return \DateTime
     */
    public function getDatetimeC()
    {
        return $this->datetimeC;
    }

    /**
     * @param \DateTime $datetimeC
     */
    public function setDatetimeC($datetimeC)
    {
        $this->datetimeC = $datetimeC;
    }
}
