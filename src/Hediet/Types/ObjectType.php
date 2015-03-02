<?php

namespace Hediet\Types;

use \ReflectionClass;

abstract class ObjectType extends Type {

    private $reflectionClass;

    /**
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
     * 
     * @param string $name
     * @return MethodInfo
     */
    public function getMethod($name)
    {
        $m = $this->getReflectionClass()->getMethod($name);
        return MethodInfo::__internal_create($this, $m);
    }


    /*
     public function isGenericType();

     public function isClosedGenericType();

     public function getGenericTypeArguments();

     public function getGenericTypeDefinition();
     */
} 