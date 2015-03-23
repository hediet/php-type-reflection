<?php

namespace Hediet\Types;

use ReflectionClass;
use ReflectionException;

/**
 * Represents either a class or an interface.
 */
abstract class ObjectType extends Type 
{
    /**
     * @var ReflectionClass
     */
    private $reflectionClass;

    /**
     * Gets the reflection class for this object type.
     * 
     * @return ReflectionClass
     */
    public function getReflectionClass()
    {
        if ($this->reflectionClass === null)
        {
            $this->reflectionClass = new ReflectionClass($this->getName());
        }
        return $this->reflectionClass;
    }

    /**
     * Gets a list of all defined methods.
     * 
     * @return MethodInfo[]
     */
    public function getMethods()
    {
        //todo cache
        $result = array();
        foreach ($this->getReflectionClass()->getMethods() as $m)
        {
            $result[] = MethodInfo::__internal_create($this, $m);
        }
        return $result;
    }

    /**
     * Gets as method by its name.
     * 
     * @param string $name
     * @return MethodInfo
     * @throws ReflectionException A reflection exception will be thrown, if the method does not exist.
     */
    public function getMethod($name)
    {
        $m = $this->getReflectionClass()->getMethod($name);
        return MethodInfo::__internal_create($this, $m);
    }

    /**
     * Checks whether the provided type is a subclass or subinterface of this type.
     * 
     * @param ObjectType $type
     * @return boolean
     */
    public function isSubtypeOf(ObjectType $type)
    {
        return $this->getReflectionClass()->isSubclassOf($type->getName());
    }
    
    
    /* TODO
     public function isGenericType();

     public function isClosedGenericType();

     public function getGenericTypeArguments();

     public function getGenericTypeDefinition();
     */
} 