<?php

namespace Hediet\Types;

use ReflectionParameter;

class ParameterInfo
{
    public static function create(ReflectionParameter $parameter)
    {
        $methodInfo = MethodInfo::create($parameter->getDeclaringFunction());
        return new ParameterInfo($methodInfo, $parameter);
    }

    public static function __internal_create(MethodInfo $declaringMethod, ReflectionParameter $parameter)
    {
        return new ParameterInfo($declaringMethod, $parameter);
    }

    /**
     * @var MethodInfo
     */
    private $declaringMethod;

    /**
     * @var ReflectionParameter
     */
    private $reflectionParameter;

    private function __construct(MethodInfo $declaringMethod, ReflectionParameter $reflectionParameter)
    {
        $this->declaringMethod = $declaringMethod;
        $this->reflectionParameter = $reflectionParameter;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->reflectionParameter->getName();
    }

    /**
     * @return MethodInfo
     */
    public function getDeclaringMethod()
    {
        return $this->declaringMethod;
    }
    
    /**
     * @return ReflectionParameter
     */
    public function getReflectionParameter()
    {
        return $this->reflectionParameter;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->declaringMethod->__internal_getParameterDescription($this->getReflectionParameter());
    }

    /**
     * @return Type
     */
    public function getType()
    {
        return $this->declaringMethod->__internal_getParameterType($this->getReflectionParameter());
    }
}