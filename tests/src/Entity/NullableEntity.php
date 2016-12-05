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

use Yamlizer\Annotation\Nullable;
use Yamlizer\Annotation\Type;

/**
 * Class NullableEntity
 *
 * @package Yamlizer\Tests\Entity
 */
class NullableEntity
{
    /**
     * @Type("string")
     * @var string
     */
    protected $nullable = 'This is nullable';

    /**
     * @Type("string")
     * @Nullable("false")
     * @var string
     */
    protected $notNullable = 'This is not nullable';

    /**
     * @return string
     */
    public function getNullable()
    {
        return $this->nullable;
    }

    /**
     * @param string $nullable
     */
    public function setNullable($nullable)
    {
        $this->nullable = $nullable;
    }

    /**
     * @return string
     */
    public function getNotNullable()
    {
        return $this->notNullable;
    }

    /**
     * @param string $notNullable
     */
    public function setNotNullable($notNullable)
    {
        $this->notNullable = $notNullable;
    }
}
