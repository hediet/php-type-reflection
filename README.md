PHP Type Reflection
===================

This library provides classes to reflect php types in an unified way.

THIS LIBRARY IS STILL IN DEVELOPMENT and serves only as preview.
More documentation will be available on first real release.

Example Usage
-------------

``` php
<?php

namespace Hediet\Types\Test;

use Hediet\Types\Type;

abstract class MyClass
{
    /**
     * Does some stuff.
     * @param MyClass $myArg Some input.
     * @return string[] The return value.
     */
    public abstract function myMethod(MyClass $myArg = null);      
}

$m = Type::ofObjectType("Hediet\Types\Test\MyClass")->getMethod("myMethod");

$this->assertEquals("Does some stuff.", $m->getDescription());
$this->assertEquals("The return value.", $m->getResultInfo()->getDescription());

/* @var $resultType \Hediet\Types\ArrayType */
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
$this->assertEquals("Hediet\Types\Test\MyClass", $myArgParam->getType()->getName());

```
