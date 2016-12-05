<?php
/**
 * This file is part of the Yamlizer package.
 *
 * (c) Pierre Waibel <waibelp85@googlemail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yamlizer\Tests\Metadata;

use Doctrine\Common\Annotations\AnnotationReader;
use Yamlizer\Cache\FileCache;
use Yamlizer\Cache\InMemoryCache;
use Yamlizer\Metadata\ClassMetadata;
use Yamlizer\Metadata\MetadataFactory;
use Yamlizer\Tests\Entity\PrimitiveEntity;

/**
 * Class MetadataFactoryTest
 *
 * @package Yamlizer\Tests\Metadata
 */
class MetadataFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AnnotationReader
     */
    protected $annotationReader;

    /**
     * @var MetadataFactory
     */
    protected $factory;

    protected function setUp()
    {
        parent::setUp();

        $this->annotationReader = new AnnotationReader();
        $this->factory          = new MetadataFactory($this->annotationReader);
        $this->factory->addCache(new InMemoryCache());
        $this->factory->addCache(new FileCache(__DIR__ . '/../../cache'));
    }

    protected function tearDown()
    {
        $this->factory          = null;
        $this->annotationReader = null;

        parent::tearDown();
    }

    public function testClassFromObject()
    {
        $metadata = $this->factory->classFromObject(new PrimitiveEntity());

        $this->assertInstanceOf(ClassMetadata::class, $metadata);
        $this->assertEquals(PrimitiveEntity::class, $metadata->getClass());
        $this->assertCount(5, $metadata->getProperties());
        $this->assertEquals(
            [
                'integer',
                'string',
                'array',
                'boolean',
                'float',
            ],
            array_keys($metadata->getProperties())
        );

        $this->assertEquals('integer', $metadata->getProperties()['integer']->getPrimitiveType());
    }

    public function testGetPropertyMetaDataTypeAnnotationMissing()
    {
        $reflectedMethod = new \ReflectionMethod($this->factory, 'getPropertyMetaData');
        $reflectedMethod->setAccessible(true);

        $reflectionProperty = new \ReflectionProperty($this->factory, 'caches');
        $this->assertNull($reflectedMethod->invokeArgs($this->factory, [$reflectionProperty]));
    }
}
