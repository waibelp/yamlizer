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

use Yamlizer\Annotation\SerializedName;
use Yamlizer\Annotation\Type;

/**
 * Class SerializedNameEntity
 *
 * @package Yamlizer\Tests\Entity
 */
class SerializedNameEntity
{
    /**
     * @Type("string")
     * @SerializedName("otherName")
     * @var string
     */
    protected $someName = '$someName content';

    /**
     * @return string
     */
    public function getSomeName()
    {
        return $this->someName;
    }

    /**
     * @param string $someName
     */
    public function setSomeName($someName)
    {
        $this->someName = $someName;
    }
}
