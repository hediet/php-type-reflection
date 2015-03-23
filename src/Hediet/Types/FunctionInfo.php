<?php

namespace Hediet\Types;

use ReflectionFunction;

//TODO this class is currently not supported

class FunctionInfo extends AbstractFunctionInfo
{
    /**
     * @param ReflectionMethod $function
     * @return MethodInfo
     */
    public static function create(ReflectionFunction $function)
    {
        return new FunctionInfo($function);
    }


    protected function __construct(ReflectionFunction $function)
    {
        parent::__construct($function);
    }

    /**
     * @return ReflectionFunction
     */
    public function getReflector()
    {
        return parent::getReflector();
    }
}