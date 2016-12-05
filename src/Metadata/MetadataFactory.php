<?php
/**
 * This file is part of the Yamlizer package.
 *
 * (c) Pierre Waibel <waibelp85@googlemail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yamlizer\Metadata;

use Doctrine\Common\Annotations\AnnotationReader;
use Yamlizer\Annotation\GetterName;
use Yamlizer\Annotation\Groups;
use Yamlizer\Annotation\Nullable;
use Yamlizer\Annotation\PreserveKeys;
use Yamlizer\Annotation\Readonly;
use Yamlizer\Annotation\SerializedName;
use Yamlizer\Annotation\SetterName;
use Yamlizer\Annotation\Type;
use Yamlizer\Annotation\YamlizerAnnotation;
use Yamlizer\Cache\ClassMetadataCacheInterface;

/**
 * Class MetadataFactory
 *
 * @package Yamlizer\Metadata
 */
class MetadataFactory
{
    /**
     * @var AnnotationReader
     */
    protected $annotationReader;

    /**
     * @var array
     */
    protected $annotationClasses = [
        Nullable::class,
        Readonly::class,
        GetterName::class,
        SetterName::class,
        SerializedName::class,
        PreserveKeys::class,
        Groups::class,
    ];

    /**
     * @var ClassMetadataCacheInterface[]
     */
    protected $caches = [];

    /**
     * Factory constructor.
     *
     * @param AnnotationReader $annotationReader
     */
    public function __construct(AnnotationReader $annotationReader)
    {
        $this->annotationReader = $annotationReader;
    }

    /**
     * @param ClassMetadataCacheInterface $cache
     */
    public function addCache(ClassMetadataCacheInterface $cache)
    {
        $this->caches[] = $cache;
    }

    /**
     * @param object|string $object
     * @return ClassMetadata|null
     */
    public function classFromObject($object)
    {
        // Check our caches
        $class = is_object($object) ? get_class($object) : $object;
        for ($i = 0; $i < count($this->caches); $i++) {
            $cache         = $this->caches[$i];
            $classMetadata = $cache->read($class);
            if ($classMetadata) {
                if ($i > 0) {
                    // Fill caches above with class metadata if missing
                    for ($j = 0; $j < $i; $j++) {
                        $this->caches[$j]->write($classMetadata);
                    }
                }

                return $classMetadata;
            }
        }

        // Extract ClassMetadata via reflection
        $reflectionClass      = new \ReflectionClass($object);
        $classMetadata        = $this->getClassMetadata($reflectionClass);
        $reflectionProperties = $reflectionClass->getProperties();
        foreach ($reflectionProperties as $reflectionProperty) {
            $propertyMetadata = $this->getPropertyMetaData($reflectionProperty);
            if ($propertyMetadata) {
                foreach ($this->annotationClasses as $annotationClass) {
                    /** @var YamlizerAnnotation $annotation */
                    $annotation = new $annotationClass();
                    $annotation->validatePropertyMetadata($propertyMetadata, $reflectionClass);
                    $annotation = null;
                }

                $classMetadata->addProperty($propertyMetadata);
            }
        }

        // Fill all caches
        foreach ($this->caches as $cache) {
            $cache->write($classMetadata);
        }

        return $classMetadata;
    }

    /**
     * @param \ReflectionClass $reflectionClass
     * @return ClassMetadata
     */
    protected function getClassMetadata(\ReflectionClass $reflectionClass)
    {
        $metadata = new ClassMetadata();
        $metadata->setClass($reflectionClass->getName());

        return $metadata;
    }

    /**
     * @param \ReflectionProperty $reflectionProperty
     * @return PropertyMetadata
     */
    protected function getPropertyMetaData(\ReflectionProperty $reflectionProperty)
    {
        /** @var Type $type */
        $type = $this->annotationReader->getPropertyAnnotation($reflectionProperty, Type::class);
        if (!$type) {
            return null;
        }

        $metadata = new PropertyMetadata();
        $metadata->setPropertyName($reflectionProperty->getName());
        $metadata->setSerializedName($metadata->getPropertyName());

        $type->assignPropertyMetadata($metadata, $type);
        foreach ($this->annotationClasses as $annotationClass) {
            /** @var YamlizerAnnotation $annotation */
            $annotation = $this->annotationReader->getPropertyAnnotation($reflectionProperty, $annotationClass);
            if ($annotation) {
                $annotation->assignPropertyMetadata($metadata, $annotation);
            }
        }

        return $metadata;
    }
}
