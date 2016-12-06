# Yamlizer

[![Build Status](https://travis-ci.org/waibelp/yamlizer.svg?branch=master)](https://travis-ci.org/waibelp/yamlizer)

Yamlizer is an PHP object serializer built with an eye on simplicity and performance.

It supports:
* Serialization of PHP objects into an array-structure which can be dumped into JSON, YAML, ...
* Deserialization of array-structure objects into PHP objects
* All scalar data types, arrays, arrays of objects and even \DateTime instances
* Easy to use annotations to control the serialization and deserialization behaviour like NULLable properties, groups and much more
* Caching of class and property metadata - no slow reflection or annotation parsing during runtime

## Installation

Use composer to require and install library in your project:

```
$ composer require waibelp/yamlizer
```

## Usage

Add annotations to classes to make them serializable:

```
namespace Acme\Entity;

use Acme\Entity\SomeOtherEntity;
use Yamlizer\Annotation\Type;

class FooBarEntity
{
    /**
     * @Type("string")
     * @var string
     */
    protected $scalarProperty = 'This is a string';
    
    /**
     * @Type("array")
     * @var array
     */
    protected $arrayProperty = [1, 2, 3];
    
    /**
     * @Type("Acme\Entity\SomeOtherEntity")
     * @var SomeOtherEntity
     */
    protected $fixedTypeProperty;
    
    /**
     * @Type("array<Acme\Entity\SomeOtherEntity>")
     * @var SomeOtherEntity[]
     */
    protected $fixedTypeList = [];
    
    /**
     * Return value of scalarProperty. Getter methods name uppercases the first letter of the property name and prepends get.
     *
     * @return string
     */
    public function getScalarProperty()
    {
        return $this->scalarProperty;
    }
    
    /**
     * Set value for scalarProperty. Setter methods name uppercases the first letter of the property name and prepends set.
     *
     * @var string $scalarProperty
     */
    public function setScalarProperty($scalarProperty)
    {
        $this->scalarProperty = $scalarProperty;
    }

    // Other getter and setter methods...
}
```

Instantiate and use ``ArraySerializer`` to serialize our object:

```
use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\Yaml\Yaml;
use Yamlizer\Metadata\MetadataFactory;
use Yamlizer\Serialization\ArraySerializer;

$metadataFactory = new MetadataFactory(new AnnotationReader());
$arraySerializer = new ArraySerializer($metadataFactory);

$object           = new FooBarEntity();
$serializedObject = $arraySerializer->serialize($object, FooBarEntity::class);

print gettype($serializedObject); // array
$json = json_encode($serializedObject);
$yaml = Yaml::dump($serializedObject);
```

Outputting ``$yaml`` results in:

```
scalarProperty: "This is a string"
arrayProperty:
    - 1
    - 2
    - 3
fixedTypeProperty: null
fixedTypeList: []
```

Outputting ``$json`` results in:

```
{
    "scalarProperty": "This is a string",
    "arrayProperty": [1, 2, 3],
    "fixedTypeProperty": null,
    "fixedTypeList": []
}
```

And deserialize it again:

```
use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\Yaml\Yaml;
use Yamlizer\Metadata\MetadataFactory;
use Yamlizer\Serialization\ArrayDeserializer;

$metadataFactory = new MetadataFactory(new AnnotationReader());
$arraySerializer = new ArrayDeserializer($metadataFactory);

/** @var FooBarEntity $object */
$array  = json_decode($json, true); // or Yaml::parse($yaml);
$object = $arrayDeserializer->deserialize($array, FooBarEntity:class);
```

## List of annotations

### Type(string)

Defines the type of data for the given property. Maybe one of PHPs scalar types or a fully qualified class name including namespace.

```
use Yamlizer\Annotation\Type;

class AcmeEntity
{
    /**
     * @Type("string")
     * @var string
     */
    protected $someString = 'This is just some string';

    /**
     * Always define format of date time string! Supports PHPs datetime format syntax.
     *
     * @Type("\DateTime<Y-m-d H:i:s>")
     * @var \DateTime
     */
    protected $dateTime;
}

$object = new AcmeEntity();
$object->setDateTime(new \DateTime('2016-26-01 01:02:03'));

// Serialization results in
// {"someString": "This is just some string", "dateTime": "2016-26-01 01:02:03"}
```

#### Valid types

| @Type                             | Internal PHP type            | Annotation                                 |
|-----------------------------------|------------------------------|--------------------------------------------|
| string                            | string                       | @Type("string")                            |
| integer                           | integer                      | @Type("integer")                           |
| float                             | float                        | @Type("float")                             |
| array                             | array                        | @Type("array")                             |
| boolean                           | boolean                      | @Type("boolean")                           |
| \DateTime<format>                 | \DateTime                    | @Type("\DateTime<Y-m-d H:i:s>")            |
| \Fully\Qualified\ClassName        | \Fully\Qualified\ClassName   | @Type("\Fully\Qualified\ClassName")        |
| array<\Fully\Qualified\ClassName> | \Fully\Qualified\ClassName[] | @Type("array<\Fully\Qualified\ClassName>") |

### SerializedName(string)

Defines the name of the serialized property. If not defined property-name is used instead.

```
use Yamlizer\Annotation\SerializedName;

class AcmeEntity
{
    /**
     * @SerializedName("entities")
     * @var Acme\Entity\SomeSpecialEntity[]
     */
    protected $specialEntities = [];
}

// Serialization results in
// {"entities": []}
```

### GetterName(string)

Defines the getters method name to retrieve a property. Default naming strategy uppercases the first letter of the property name and prepends get.

```
use Yamlizer\Annotation\GetterName;
use Yamlizer\Annotation\Type;

class AcmeEntity
{
    /**
     * @Type("string")
     * @GetterName("computeSomeString")
     * @var string
     */
    protected $someString = 'string';
    
    public function computeSomeString()
    {
        return 'Awesome ' . $this->someString;
    }
}
```

### SetterName(string)

Defines the setters method name to retrieve a property. Default naming strategy uppercases the first letter of the property name and prepends set.

```
use Yamlizer\Annotation\Type;
use Yamlizer\Annotation\SetterName;

class AcmeEntity
{
    /**
     * @Type("string")
     * @SetterName("setString")
     * @var string
     */
    protected $someString = 'string';
    
    /**
     * @var string $string
     */
    public function setString($string)
    {
        $this->someString = $string;
    }
}
```

### Nullable(boolean)

Whether the property is nullable or not. Throws an ``NullValueException`` when deserializing null values. Defaults to true.

```
use Yamlizer\Annotation\Nullable;

class AcmeEntity
{
    /**
     * @Type("array<Acme\Entity\SomeSpecialEntity>")
     * @Nullable(false)
     * @var Acme\Entity\SomeSpecialEntity[]
     */
    protected $specialEntity = [];
}
```

### Readonly(boolean)

Whether the property is readonly or not. Does not deserialize values to readonly properties. Defaults to false.

```
use Yamlizer\Annotation\Readonly;

class AcmeEntity
{
    /**
     * @Readonly(true)
     * @var Acme\Entity\SomeSpecialEntity[]
     */
    protected $specialEntity = [];
}
```

### PreserveKeys(boolean)

Only affects arrays. Whether the array keys should be preserved or thrown away during serialization.

```
use Yamlizer\Annotation\PreserveKeys;

class AcmeEntity
{
    /**
     * @PreserveKeys(true)
     * @var array
     */
    protected $myArray = [
        'one' => 1,
        'two' => 2,
    ];
}
```

### Groups(string[])

Each property can have one or multiple groups assigned which can be used to control which properties should be mapped during serialization and deserialization.

```
use Yamlizer\Annotation\Groups;

class AcmeEntity
{
    /**
     * @Groups("a, b")
     * @var int
     */
    protected $integerOne = 1;
    
    /**
     * @Groups("a")
     * @var int
     */
    protected $integerTwo = 2;
}
```

## Advanced topics

### Using groups to control properties to be serialized and deserialized

Imagine an object having properties in different groups:

```
class GroupEntity
{
    /**
     * @Type("integer")
     * @Groups("odd, all")
     * @var int
     */
    protected $one = 1;

    /**
     * @Type("integer")
     * @Groups("even, all")
     * @var int
     */
    protected $two = 2;

    /**
     * @Type("integer")
     * @Groups("odd, all")
     * @var int
     */
    protected $three = 3;

    /**
     * @Type("integer")
     * @Groups("even, all")
     * @var int
     */
    protected $four = 4;
```

Now simply set up our ``Context`` object to set up groups for our class:


```
use Doctrine\Common\Annotations\AnnotationReader;
use Yamlizer\Metadata\MetadataFactory;
use Yamlizer\Metadata\ClassMetadata;
use Yamlizer\Serialization\ArraySerializer;
use Yamlizer\Serialization\Context;

$metadataFactory = new MetadataFactory(new AnnotationReader());
$arraySerializer = new ArraySerializer($metadataFactory);

$context = new Context();
$object  = new GroupEntity();

// Having no groups set results in all properties
var_dump($arraySerializer->serialize($object, GroupEntity::class, $context)); // ['one' => 1, 'two' => 2, 'three' => 3, 'four' => 4]

// Skip all properties not in group even
$context->setGroups(GroupEntity::class, ['even']);
var_dump($arraySerializer->serialize($object, GroupEntity::class, $context)); // ['two' => 2, 'four' => 4]

// Skip all properties not in group odd
$context->setGroups(GroupEntity::class, ['odd']);
var_dump($arraySerializer->serialize($object, GroupEntity::class, $context)); // ['one' => 1, 'three' => 3]
```


### Caching

Yamlizer is shipped with two caches: ``InMemoryCache`` and ``FileCache``:

```
use Doctrine\Common\Annotations\AnnotationReader;
use Yamlizer\Cache\FileCache;
use Yamlizer\Cache\InMemoryCache;
use Yamlizer\Metadata\MetadataFactory;
use Yamlizer\Metadata\ClassMetadata;
use Yamlizer\Serialization\ArraySerializer;

$metadataFactory = new MetadataFactory(new AnnotationReader());
$arraySerializer = new ArraySerializer($metadataFactory);

$cacheDirectoryPath = __DIR__ . '/some/writeable/cache/directory';
$arraySerializer->addCache(new InMemoryCache());
$arraySerializer->addCache(new FileCache($cacheDirectoryPath));
```

### Exceptions

Serializing objects into data and vice versa can always throw exceptions in case of wrong or missing data.

To catch those exceptions simply wrap calls into a try-catch-block:

```
use Yamlizer\Exception\InvalidTypeException;
use Yamlizer\Exception\NullValueException;
use Yamlizer\Exception\YamlizerException;

$metadataFactory = new MetadataFactory(new AnnotationReader());
$arraySerializer = new ArraySerializer($metadataFactory);
// ...

try {
    $arraySerializer->serialize($object);
} catch (NullValueException $e1) {
    // Catch exceptions caused by null values
} catch (InvalidTypeException $e2) {
    // Catch exceptions caused by invalid types
} catch (YamlizerException $e3) {
    // Fetch all kinds of exceptions thrown from Yamlizer package
}
```

## Contribute

1. Fork repo and implement fix
2. Extend tests and validate with ``bin/phpunit``
3. Check code coverage ``tests/build/report/index.html``
4. Check code style ``bin/phpcs --standard=PSR2 src/``
5. Submit pull request
