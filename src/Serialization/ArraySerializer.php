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
 * Class ArraySerializer
 *
 * @package Yamlizer\Serialization
 */
class ArraySerializer
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
     * @param object|object[] $object
     * @param string          $class
     * @param Context         $context
     * @return array
     */
    public function serialize($object, $class, Context $context = null)
    {
        if (is_array($object)) {
            $objectList = [];
            foreach ($object as $key => $item) {
                $objectList[$key] = $this->serialize($item, $class, $context);
            }

            return $objectList;
        }

        if (!$context) {
            $context = $this->defaultContext;
        }

        $array         = [];
        $groups        = $context->getGroups($class);
        $classMetadata = $this->metadataFactory->classFromObject($class);
        foreach ($classMetadata->getProperties() as $property) {
            if (count($groups) > 0 && !$property->isPropertyInGroup($groups)) {
                continue;
            }

            $key         = $property->getSerializedName();
            $value       = $object->{$property->getGetterName()}();
            $array[$key] = $this->serializeProperty($property, $value, $context);
        }

        return $array;
    }

    /**
     * @param PropertyMetadata $metadata
     * @param mixed            $value
     * @param Context          $context
     * @return mixed
     * @throws NullValueException
     */
    protected function serializeProperty(PropertyMetadata $metadata, $value, Context $context)
    {
        $serializedValue = $value;

        $type = $metadata->getPrimitiveType();
        if (PropertyMetadata::TYPE_ARRAY === $type) {
            $serializedValue = $this->serializeArray($metadata, $value, $metadata->getComplexType(), $context);
        } elseif (PropertyMetadata::TYPE_DATETIME === $type) {
            $serializedValue = $this->serializeDateTime($metadata, $value);
        } elseif (PropertyMetadata::TYPE_OBJECT === $type) {
            $serializedValue = $this->serializeObject($metadata, $value, $context);
        }

        if (null === $serializedValue && !$metadata->isNullable()) {
            throw new NullValueException('Property ' . $metadata->getPropertyName() . ' is not nullable');
        }

        return $serializedValue;
    }

    /**
     * @param PropertyMetadata $metadata
     * @param array            $value
     * @param string|null      $class
     * @param Context          $context
     * @return array
     * @throws InvalidTypeException
     */
    protected function serializeArray(PropertyMetadata $metadata, $value, $class, Context $context)
    {
        if (!is_array($value) && null !== $value) {
            $msg = 'Expected ' . PropertyMetadata::TYPE_ARRAY . ' instead got ' . gettype($value);
            $e   = new InvalidTypeException($msg);
            $e->setActualType(gettype($value));
            $e->setExpectedType(PropertyMetadata::TYPE_ARRAY);

            throw $e;
        }

        if (null === $value || 0 === count($value)) {
            return [];
        }

        $serializedValue = [];
        if ($class) {
            $groups = $context->getGroups($class);
            foreach ($value as $key => $object) {
                if (count($groups) > 0 && !$metadata->isPropertyInGroup($groups)) {
                    continue;
                }

                $serializedValue[$key] = $this->serialize($object, $class, $context);
            }
        } else {
            foreach ($value as $key => $object) {
                $serializedValue[$key] = $object;
            }
        }

        if (!$metadata->isPreserveKeys()) {
            $serializedValue = array_values($serializedValue);
        }

        return $serializedValue;
    }

    /**
     * @param PropertyMetadata $metadata
     * @param \DateTime        $value
     * @return string
     * @throws InvalidTypeException
     */
    protected function serializeDateTime(PropertyMetadata $metadata, $value)
    {
        if (!($value instanceof \DateTime) && null !== $value) {
            $msg = 'Expected \DateTime instead got ' . gettype($value);
            $e   = new InvalidTypeException($msg);
            $e->setActualType(gettype($value));
            $e->setExpectedType('\DateTime');

            throw $e;
        }

        return $value ? $value->format($metadata->getComplexType()) : null;
    }

    /**
     * @param PropertyMetadata $metadata
     * @param object           $value
     * @param Context          $context
     * @return array
     * @throws InvalidTypeException
     */
    protected function serializeObject(PropertyMetadata $metadata, $value, Context $context)
    {
        if (null === $value) {
            return null;
        }

        $class = $metadata->getComplexType();
        if ($class !== get_class($value)) {
            $msg = 'Expected ' . $metadata->getComplexType() . ' instead got ' . get_class($value);
            $e   = new InvalidTypeException($msg);
            $e->setActualType(get_class($value));
            $e->setExpectedType($metadata->getComplexType());

            throw $e;
        }

        return $this->serialize($value, $class, $context);
    }
}
