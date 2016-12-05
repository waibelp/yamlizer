<?php
/**
 * This file is part of the Yamlizer package.
 *
 * (c) Pierre Waibel <waibelp85@googlemail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yamlizer\Serialization;

use Yamlizer\Exception\InvalidTypeException;
use Yamlizer\Exception\NullValueException;
use Yamlizer\Metadata\MetadataFactory;
use Yamlizer\Metadata\PropertyMetadata;

/**
 * Class ArrayDeserializer
 *
 * @package Yamlizer\Serialization
 */
class ArrayDeserializer
{
    /**
     * @var MetadataFactory
     */
    protected $metadataFactory;

    /**
     * @var Context
     */
    protected $defaultContext;

    /**
     * ArraySerializer constructor.
     *
     * @param MetadataFactory $metadataFactory
     */
    public function __construct(MetadataFactory $metadataFactory)
    {
        $this->metadataFactory = $metadataFactory;
        $this->defaultContext  = new Context();
    }

    /**
     * @param Context $defaultContext
     */
    public function setDefaultContext(Context $defaultContext)
    {
        $this->defaultContext = $defaultContext;
    }

    /**
     * @param array       $array
     * @param string      $class
     * @param object|null $object
     * @param Context     $context
     * @return object|object[]
     * @throws NullValueException
     */
    public function deserialize(array $array, $class, $object = null, Context $context = null)
    {
        if (preg_match('/^array<(.*)>$/', trim($class), $matches)) {
            $class   = trim($matches[1]);
            $objects = [];
            foreach ($array as $item) {
                $objects[] = $this->deserialize($item, $class, $object ? clone $object : null, $context);
            }

            return $objects;
        }

        if (!$context) {
            $context = $this->defaultContext;
        }

        if (!$object) {
            $object = new $class();
        }

        $groups        = $context->getGroups($class);
        $classMetadata = $this->metadataFactory->classFromObject($class);
        foreach ($classMetadata->getProperties() as $property) {
            if ($property->isReadonly()) {
                continue;
            }

            if (count($groups) > 0 && !$property->isPropertyInGroup($groups)) {
                continue;
            }

            $key   = $property->getSerializedName();
            $value = isset($array[$key]) ? $array[$key] : null;
            if (null === $value && !$property->isNullable()) {
                $msg = sprintf('Property %s of %s is not nullable', $key, $classMetadata->getClass());
                $e   = new NullValueException($msg);
                $e->setClass($classMetadata->getClass());
                $e->setKey($key);

                throw $e;
            }

            if (isset($array[$key])) {
                $type = $property->getPrimitiveType();
                if (PropertyMetadata::TYPE_ARRAY === $type) {
                    $value = $this->deserializeArray($value, $property, $context);
                } elseif (PropertyMetadata::TYPE_DATETIME === $type) {
                    $value = $this->deserializeDateTime($value, $property);
                } elseif (PropertyMetadata::TYPE_OBJECT === $type) {
                    $value = $this->deserializeObject($value, $property, $context);
                }

                $object->{$property->getSetterName()}($value);
            }
        }

        return $object;
    }

    /**
     * @param array            $array
     * @param PropertyMetadata $propertyMetadata
     * @param Context          $context
     * @return array
     * @throws InvalidTypeException
     */
    protected function deserializeArray($array, PropertyMetadata $propertyMetadata, Context $context)
    {
        if (!is_array($array)) {
            if ($propertyMetadata->isNullable()) {
                return null;
            }

            $e = new InvalidTypeException('Expected ' . PropertyMetadata::TYPE_ARRAY . ' got ' . gettype($array));
            $e->setKey($propertyMetadata->getSerializedName());
            $e->setActualType(gettype($array));
            $e->setExpectedType(PropertyMetadata::TYPE_ARRAY);

            throw $e;
        }

        if ($propertyMetadata->getComplexType()) {
            $objects = [];
            foreach ($array as $key => $record) {
                $object = $this->deserializeObject($record, $propertyMetadata, $context);
                if ($object) {
                    $objects[$key] = $object;
                }
            }

            return $objects;
        } else {
            return $array;
        }
    }

    /**
     * @param array            $object
     * @param PropertyMetadata $propertyMetadata
     * @param Context          $context
     * @return null|object
     * @throws InvalidTypeException
     */
    protected function deserializeObject($object, PropertyMetadata $propertyMetadata, Context $context)
    {
        if (!is_array($object)) {
            if ($propertyMetadata->isNullable()) {
                return null;
            }

            $e = new InvalidTypeException(
                'Expected ' . $propertyMetadata->getComplexType() . ' got ' . gettype($object)
            );
            $e->setKey($propertyMetadata->getSerializedName());
            $e->setActualType(gettype($object));
            $e->setExpectedType(PropertyMetadata::TYPE_ARRAY);

            throw $e;
        }

        return $this->deserialize($object, $propertyMetadata->getComplexType(), null, $context);
    }

    /**
     * @param string           $string
     * @param PropertyMetadata $propertyMetadata
     * @return \DateTime|null
     * @throws InvalidTypeException
     */
    protected function deserializeDateTime($string, PropertyMetadata $propertyMetadata)
    {
        if (!is_string($string)) {
            if ($propertyMetadata->isNullable()) {
                return null;
            }

            $e = new InvalidTypeException('Expected ' . PropertyMetadata::TYPE_STRING . ' got ' . gettype($string));
            $e->setKey($propertyMetadata->getSerializedName());
            $e->setActualType(gettype($string));
            $e->setExpectedType(PropertyMetadata::TYPE_STRING);

            throw $e;
        }

        $dateFormat = $propertyMetadata->getComplexType();

        return \DateTime::createFromFormat($dateFormat, $string);
    }
}
