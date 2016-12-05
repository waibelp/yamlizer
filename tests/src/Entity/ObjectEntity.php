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
 * Class ObjectEntity
 *
 * @package Yamlizer\Tests\Entity
 */
class ObjectEntity
{
    /**
     * @Type("Yamlizer\Tests\Entity\DateTimeEntity")
     * @Groups("one,two")
     * @var DateTimeEntity
     */
    protected $dateTime;

    /**
     * @Type("array<Yamlizer\Tests\Entity\ObjectEntity>")
     * @Groups("one")
     * @var ObjectEntity[]
     */
    protected $subObjects = [];

    /**
     * @return DateTimeEntity
     */
    public function getDateTime()
    {
        return $this->dateTime;
    }

    /**
     * @param DateTimeEntity $dateTime
     * @return $this
     */
    public function setDateTime($dateTime)
    {
        $this->dateTime = $dateTime;

        return $this;
    }

    /**
     * @return ObjectEntity[]
     */
    public function getSubObjects()
    {
        return $this->subObjects;
    }

    /**
     * @param ObjectEntity[] $subObjects
     * @return $this
     */
    public function setSubObjects($subObjects)
    {
        $this->subObjects = $subObjects;

        return $this;
    }
}
