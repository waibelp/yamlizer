<?php
/**
 * This file is part of the Yamlizer package.
 *
 * (c) Pierre Waibel <waibelp85@googlemail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yamlizer\Cache;

use Yamlizer\Metadata\ClassMetadata;
use Yamlizer\Metadata\PropertyMetadata;

/**
 * Class InMemoryCache
 *
 * @package Yamlizer\Cache
 */
class FileCache implements ClassMetadataCacheInterface
{
    /**
     * @var string
     */
    protected $cacheDirectoryPath;

    /**
     * @param string $cacheDirectoryPath
     */
    public function __construct($cacheDirectoryPath)
    {
        $this->cacheDirectoryPath = $cacheDirectoryPath;
        if (!is_dir($this->cacheDirectoryPath) || !is_writable($this->cacheDirectoryPath)) {
            throw new \InvalidArgumentException(
                sprintf('Cache directory does not exist or is not writeable: %s', $this->cacheDirectoryPath)
            );
        }
    }

    /**
     * @param string $class
     * @return ClassMetadata|null
     */
    public function read($class)
    {
        $classFilePath = $this->getCacheFilePathForClass($class);
        if (!file_exists($classFilePath)) {
            return null;
        }

        return $this->deserializeClassMetadataArray(include($classFilePath));
    }

    /**
     * @param ClassMetadata $classMetadata
     * @return bool
     */
    public function write(ClassMetadata $classMetadata)
    {
        $properties = [];
        foreach ($classMetadata->getProperties() as $property) {
            $properties[] = [
                'propertyName'   => $property->getPropertyName(),
                'serializedName' => $property->getSerializedName(),
                'getterName'     => $property->getGetterName(),
                'setterName'     => $property->getSetterName(),
                'primitiveType'  => $property->getPrimitiveType(),
                'complexType'    => $property->getComplexType(),
                'nullable'       => $property->isNullable(),
                'readonly'       => $property->isReadonly(),
                'preserveKeys'   => $property->isPreserveKeys(),
                'groups'         => $property->getGroups(),
            ];
        }

        $array = [
            'class'      => $classMetadata->getClass(),
            'properties' => $properties,
        ];

        return file_put_contents(
            $this->getCacheFilePathForClass($classMetadata->getClass()),
            str_replace(
                PHP_EOL,
                ' ',
                sprintf('<?php /* %s */ return %s;', date('Y-m-d H:i:s'), var_export($array, true))
            )
        ) > 0;
    }

    /**
     * @param string $class
     * @return string
     */
    protected function getCacheFilePathForClass($class)
    {
        return sprintf('%s/%s.php', $this->cacheDirectoryPath, md5($class));
    }

    /**
     * @param array $array
     * @return ClassMetadata
     */
    protected function deserializeClassMetadataArray($array)
    {
        $metadata = new ClassMetadata();
        $metadata->setClass($array['class']);
        foreach ($array['properties'] as $propertyName => $propertyArray) {
            $metadata->addProperty($this->deserializePropertyMetadataArray($propertyArray));
        }

        return $metadata;
    }

    /**
     * @param $array
     * @return PropertyMetadata
     */
    protected function deserializePropertyMetadataArray($array)
    {
        $metadata = new PropertyMetadata();
        $metadata->setPropertyName($array['propertyName']);
        $metadata->setSerializedName($array['serializedName']);
        $metadata->setPrimitiveType($array['primitiveType']);
        $metadata->setComplexType($array['complexType']);
        $metadata->setGetterName($array['getterName']);
        $metadata->setSetterName($array['setterName']);
        $metadata->setReadonly($array['readonly']);
        $metadata->setNullable($array['nullable']);
        $metadata->setPreserveKeys($array['preserveKeys']);
        $metadata->setGroups($array['groups']);

        return $metadata;
    }
}
