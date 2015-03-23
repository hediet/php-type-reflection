<?php

namespace Hediet\Types;

use ReflectionParameter;

/**
 * Represents a parameter with description and type information.
 */
class ParameterInfo
{
    public static function create(ReflectionParameter $parameter)
    {
        $methodInfo = MethodInfo::create($parameter->getDeclaringFunction());
        return new ParameterInfo($methodInfo, $parameter);
    }

    public static function __internal_create(AbstractFunctionInfo $declaringFunction, ReflectionParameter $parameter)
    {
        return new ParameterInfo($declaringFunction, $parameter);
    }

    /**
     * @var MethodInfo
     */
    private $declaringFunction;

    /**
     * @var ReflectionParameter
     */
    private $reflector;

    private function __construct(AbstractFunctionInfo $declaringFunction, ReflectionParameter $reflector)
    {
        $this->declaringFunction = $declaringFunction;
        $this->reflector = $reflector;
    }

    /**
     * Gets the name of the parameter.
     * 
     * @return string
     */
    public function getName()
    {
        return $this->reflector->getName();
    }

    /**
     * Gets the function or method which declares the parameter.
     * 
     * @return AbstractFunctionInfo
     */
    public function getDeclaringFunction()
    {
        return $this->declaringFunction;
    }
    
    /**
     * Gets the reflector for this parameter.
     * 
     * @return ReflectionParameter
     */
    public function getReflector()
    {
        return $this->reflector;
    }

    /**
     * Gets the description of the parameter.
     * 
     * @return string
     */
    public function getDescription()
    {
        return $this->declaringFunction->__internal_getParameterDescription(
                $this->getReflector());
    }

    /**
     * Gets the type of the parameter.
     * 
     * @return Type
     */
    public function getType()
    {
        return $this->declaringFunction->__internal_getParameterType(
                $this->getReflector());
    }
}