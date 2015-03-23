<?php

namespace Hediet\Types;

use ReflectionMethod;

class MethodInfo extends AbstractFunctionInfo
{
    /**
     * @param ReflectionMethod $method
     * @return MethodInfo
     */
    public static function create(ReflectionMethod $method)
    {
        $declaringType = Type::byReflectionClass($method->getDeclaringClass());
        return new MethodInfo($declaringType, $method);
    }

    /**
     * @param ObjectType $declaringType
     * @param ReflectionMethod $method
     * @return MethodInfo
     */
    public static function __internal_create(ObjectType $declaringType, ReflectionMethod $method)
    {
        return new MethodInfo($declaringType, $method);
    }


    /**
     * @var ObjectType
     */
    private $declaringType;

    protected function __construct(ObjectType $declaringType, ReflectionMethod $reflector)
    {
        parent::__construct($reflector);
        
        $this->declaringType = $declaringType;
    }

    /**
     * @return ReflectionMethod
     */
    public function getReflector()
    {
        return parent::getReflector();
    }

    /**
     * Gets the object type which declares this method.
     * 
     * @return ObjectType
     */
    public function getDeclaringType()
    {
        return $this->declaringType;
    }
}