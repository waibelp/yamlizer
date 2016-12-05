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

use Yamlizer\Annotation\Readonly;
use Yamlizer\Annotation\Type;

/**
 * Class ReadonlyEntity
 *
 * @package Yamlizer\Tests\Entity
 */
class ReadonlyEntity
{
    /**
     * @Type("string")
     * @Readonly(true)
     * @var string
     */
    protected $readable = 'readonly';

    /**
     * @Type("string")
     * @var string
     */
    protected $writeable = 'writeable';

    /**
     * @return string
     */
    public function getReadable()
    {
        return $this->readable;
    }

    /**
     * @param string $readable
     */
    public function setReadable($readable)
    {
        $this->readable = $readable;
    }

    /**
     * @return string
     */
    public function getWriteable()
    {
        return $this->writeable;
    }

    /**
     * @param string $writeable
     */
    public function setWriteable($writeable)
    {
        $this->writeable = $writeable;
    }
}
