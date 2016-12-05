<?php
/**
 * This file is part of the Yamlizer package.
 *
 * (c) Pierre Waibel <waibelp85@googlemail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yamlizer\Tests\Cache;

use Yamlizer\Cache\FileCache;

/**
 * Class FileCacheTest
 *
 * @package Yamlizer\Tests\Cache
 */
class FileCacheTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var FileCache
     */
    protected $cache;

    protected function setUp()
    {
        parent::setUp();

        $this->cache = new FileCache(__DIR__ . '/../../cache');
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Cache directory does not exist or is not writeable: ./cache
     */
    public function testInstantiateWithInvalidCacheDirectory()
    {
        new FileCache('./cache');
    }
}
