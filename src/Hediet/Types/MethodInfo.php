<?php

namespace Hediet\Types;

use Hediet\Types\Helper\DocBlockParser;
use Hediet\Types\Helper\ParseMethodBlockResult;
use Hediet\Types\Helper\ParserClassNameResolver;
use ReflectionMethod;
use ReflectionParameter;

class MethodInfo
{
    /**
     * @param ReflectionMethod $reflectionMethod
     * @return MethodInfo
     */
    public static function create(ReflectionMethod $reflectionMethod)
    {
        $declaringType = Type::byReflectionClass($reflectionMethod->getDeclaringClass());
        return new MethodInfo($declaringType, $reflectionMethod);
    }

    /**
     * @param ObjectType $declaringType
     * @param ReflectionMethod $reflectionMethod
     * @return MethodInfo
     */
    public static function __internal_create(ObjectType $declaringType, ReflectionMethod $reflectionMethod)
    {
        return new MethodInfo($declaringType, $reflectionMethod);
    }


    /**
     * @var ObjectType
     */
    private $declaringType;

    /**
     * @var ReflectionMethod
     */
    private $reflectionMethod;

    /**
     * @var ParseMethodBlockResult
     */
    private $parseResult;

    /**
     *
     * @var ShortClassNameResolver
     */
    private $resolver;

    private $initialized = false;

    /**
     * @var Type[string]
     */
    private $parameterTypeCache = array();

    private $resultInfo;
    
    
    private function __construct(ObjectType $declaringType, ReflectionMethod $reflectionMethod)
    {
        $this->declaringType = $declaringType;
        $this->reflectionMethod = $reflectionMethod;
    }


    /**
     * @return string
     */
    public function getName()
    {
        return $this->reflectionMethod->getName();
    }

    /**
     * @return ReflectionMethod
     */
    public function getReflectionMethod()
    {
        return $this->reflectionMethod;
    }

    /**
     * @return ObjectType
     */
    public function getDeclaringType()
    {
        return $this->declaringType;
    }


    private function initialize()
    {
        $this->initialized = true;
        
        $this->parseResult = DocBlockParser::parseMethodDocBlock($this->reflectionMethod);
        $this->resolver = new ParserClassNameResolver($this);
        
        if ($this->parseResult->resultInfo != null)
        {
            $description = $this->parseResult->resultInfo->description;
            $type = Type::of($this->parseResult->resultInfo->type, $this->resolver);
            $this->resultInfo = new ResultInfo($description, $type);
        }
    }


    /**
     * @return string
     */
    public function getDescription()
    {
        if (!$this->initialized)
            $this->initialize();
        return $this->parseResult->description;
    }


    /**
     * @return ResultInfo
     */
    public function getResult()
    {
        if (!$this->initialized)
            $this->initialize();
        return $this->resultInfo;
    }


    /**
     * @param ReflectionParameter $parameter
     * @return string
     */
    function __internal_getParameterDescription(ReflectionParameter $parameter)
    {
        if (!$this->initialized)
            $this->initialize();
        return $this->parseResult->parameter[$parameter->getName()]->description;
    }

    /**
     * @param ReflectionParameter $parameter
     * @return Type
     */
    function __internal_getParameterType(ReflectionParameter $parameter)
    {
        if (!isset($this->parameterTypeCache[$parameter->name]))
        {
            if (!$this->initialized)
                $this->initialize();

            $typeStr = $this->parseResult->parameter[$parameter->getName()]->type;
            $type = Type::of($typeStr, $this->resolver);

            $this->parameterTypeCache[$parameter->name] = $type;
        }
        
        return $this->parameterTypeCache[$parameter->name];
    }

    /**
     * @return ParameterInfo[]
     */
    public function getParameters()
    {
        //TODO cache
        $result = array();
        foreach ($this->reflectionMethod->getParameters() as $p)
            $result[] = ParameterInfo::__internal_create($this, $p);
        return $result;
    }
}