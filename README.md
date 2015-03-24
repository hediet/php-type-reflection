PHP Type Reflection
===================

This library provides classes to reflect php types in an unified way.

Installation
------------
You can use [Composer](http://getcomposer.org/) to download and install PHP Type Reflection.
To add PHP Type Reflection to your project, simply add a dependency on hediet/type-reflection to your project's `composer.json` file.

Here is a minimal example of a `composer.json` file that just defines a dependency on PHP Type Reflection:

``` json
{
    "require": {
        "hediet/type-reflection": "^0.1.2"
    }
}
```

Usage
-----

### Static Methods of the Type Class ###

There are several static methods to get a type instance:
``` php
<?php

use Hediet\Types\Type;

// Primitive types
Type::ofInteger();
Type::ofFloat();
Type::ofString();
Type::ofBoolean();
Type::ofMixed();
Type::ofResource();
Type::ofObject();
Type::ofNull();

// Non-primitive types
Type::ofArray($itemType); //$itemType must be a Type
Type::ofObjectType($fullName); //can be either a class or interface
Type::ofClass($fullName);
Type::ofInterface($fullName);
Type::ofUnion($types); //$types must be a Type array
Type::ofNullable($type); //$type must be a Type. Returns $type|null.

Type::byValue($value);
Type::byReflectionClass($reflectionClass);
Type::of($typeName); //$typeName can be an arbitrary type name
Type::of($typeName, $resolver); //A resolver can be used to resolve relative class names.

?>
```

### Methods of the Type Class ###

There are several methods defined for type instances:
``` php
<?php

// Common methods
$type->getName(); //Gets an unique name of $type. Two equal types have the same name.
$type->isAssignableFrom($otherType); //Checks whether values of $otherType can be assigned to $type.
$type->isAssignableFromValue($value); //Checks whether $value can be assigned to $type.
$type->equals($otherType); //Checks whether $type is equal to $otherType.

// Methods for ObjectType
$type->getReflectionClass();
$type->getMethods(); //Gets a MethodInfo array.
$type->getMethod($name);
$type->isSubtypeOf($otherType);

// Methods for ClassType
$type->getProperties(); //Gets a PropertyInfo array.
$type->getProperty();
$type->isImplementorOf($interfaceType);
$type->getImplementedInterfaces();

// Methods for ArrayType
$type->getKeyType();
$type->getItemType();

// Methods for UnionType
$type->getUnitedTypes();

?>
```

### MethodInfo, ParameterInfo, ResultInfo and PropertyInfo ###

Alls these info classes provide type information and a description by parsing the php doc comments.

### Examples ###

``` php
<?php

namespace Hediet\Types\Test;

use Hediet\Types\Type;
use Hediet\Types\ArrayType;

interface FooInterface
{
    
}

class Foo implements FooInterface
{
    
}

abstract class MyClass
{
    /**
     * The name.
     * @var MyClass|string
     */
    public $name;
    
    /**
     * Does some stuff.
     * @param Type|MyClass|ArrayType $myArg Some input.
     * @return string[] The return value.
     */
    public abstract function myMethod($myArg);
}

$p = Type::ofClass("Hediet\\Types\\Test\\MyClass")->getProperty("name");
$this->assertEquals("The name.", $p->getDescription());
$this->assertEquals("Hediet\\Types\\Test\\MyClass|string", $p->getType()->getName());


$m = Type::ofObjectType("Hediet\Types\Test\MyClass")->getMethod("myMethod");

$this->assertEquals("Does some stuff.", $m->getDescription());
$this->assertEquals("The return value.", $m->getResultInfo()->getDescription());

/* @var $resultType ArrayType */
$resultType = $m->getResultInfo()->getType();
$this->assertEquals("string[]", $resultType->getName());
$this->assertEquals("string", $resultType->getItemType()->getName());
$this->assertTrue($resultType->isAssignableFromValue(array("str1", "str2")));
$this->assertFalse($resultType->isAssignableFromValue(array(1, 2)));

$this->assertFalse($resultType->isAssignableFrom(Type::ofMixed()));
$this->assertTrue(Type::ofMixed()->isAssignableFrom($resultType));

$myArgParam = $m->getParameters()[0];
$this->assertEquals("myArg", $myArgParam->getName());
$this->assertEquals("Some input.", $myArgParam->getDescription());
$this->assertEquals("Hediet\Types\Test\MyClass|Hediet\Types\Type", $myArgParam->getType()->getName());


//Union Types
$fooInterface = Type::of("Hediet\Types\Test\FooInterface");
$foo = Type::of("Hediet\Types\Test\Foo");
$fooOrFooInterface = Type::ofUnion(array($fooInterface, $foo));

$this->assertTrue($fooOrFooInterface->equals($fooInterface));
$this->assertTrue(
        Type::ofUnion(array($fooInterface, Type::ofBoolean()))
                ->isAssignableFrom($foo));

$this->assertTrue(
        Type::ofUnion(array($fooInterface, Type::ofBoolean()))
                ->isAssignableFrom(Type::ofBoolean()));

$this->assertFalse(
        Type::ofUnion(array($foo, Type::ofBoolean()))
                ->isAssignableFrom($fooInterface));

$this->assertFalse(
        Type::ofUnion(array($foo, Type::ofBoolean()))->isAssignableFrom(
                Type::ofUnion(array($foo, Type::ofBoolean(), Type::ofNull()))));

$this->assertTrue(
        Type::ofUnion(array($foo, Type::ofBoolean(), Type::ofNull()))->isAssignableFrom(
                Type::ofUnion(array(Type::ofBoolean(), $foo))));

?>
```

Class Diagram
-------------
![class diagram](docs/class-diagram.png)


TODO
----
* Add support for FunctionInfo.  
  This requires to parse the use statements manually, as the StaticReflectionParser does not support reflecting functions.
* Add support for generic classes, methods and functions (like [this](https://gist.github.com/mvriel/3823010)).
* Enable reflecting types which are not loaded.

Author
------
Henning Dieterichs - henning.dieterichs@hediet.de

License
-------
PHP Expect is licensed under the MIT License.