<?php

namespace Hediet\Types;

use Hediet\Types\Helper\DocBlockParser;
use Hediet\Types\Helper\ParseMethodBlockResult;
use Hediet\Types\Helper\ParserClassNameResolver;
use ReflectionFunction;
use ReflectionFunctionAbstract;
use ReflectionMethod;
use ReflectionParameter;
use RuntimeException;

class AbstractFunctionInfo
{
    /**
     * @param ReflectionFunctionAbstract $function
     * @return AbstractFunctionInfo
     */
    public static function create(ReflectionFunctionAbstract $function)
    {
        if ($function instanceof ReflectionMethod)
            return MethodInfo::create($function);
        //else if ($function instanceof ReflectionFunction) // FunctionInfo is currently not supported
        //    return FunctionInfo::create($function); 
        else
            throw new RuntimeException("reflector is not supported");
    }

    
    /**
     * @var ReflectionFunctionAbstract
     */
    private $reflector;

    /**
     * @var ParseMethodBlockResult
     */
    private $parseResult;

    /**
     * @var RelativeClassNameResolver
     */
    private $resolver;

    private $initialized = false;

    /**
     * @var Type[string]
     */
    private $parameterTypeCache = array();

    private $resultInfo;
    
    
    protected function __construct(ReflectionFunctionAbstract $function)
    {
        $this->reflector = $function;
    }


    /**
     * Gets the name of the method or function.
     * 
     * @return string
     */
    public function getName()
    {
        return $this->reflector->getName();
    }

    /**
     * Gets the reflector for this function or method.
     * 
     * @return ReflectionFunctionAbstract
     */
    public function getReflector()
    {
        return $this->reflector;
    }


    private function initialize()
    {
        $this->initialized = true;
        
        $this->parseResult = DocBlockParser::parseFunctionDocBlock($this->reflector);
        //TODO getDeclaringType is not available for FunctionInfo
        $this->resolver = new ParserClassNameResolver($this->getDeclaringType()->getName());
        
        if ($this->parseResult->resultInfo != null)
        {
            $description = $this->parseResult->resultInfo->description;
            $type = Type::of($this->parseResult->resultInfo->type, $this->resolver);
            $this->resultInfo = new ResultInfo($description, $type);
        }
    }


    /**
     * Gets the description of the method.
     * 
     * @return string
     */
    public function getDescription()
    {
        if (!$this->initialized)
            $this->initialize();
        return $this->parseResult->description;
    }


    /**
     * Gets an info object for the result of this method.
     * Will be null, if this method does not return a result.
     * 
     * @return ResultInfo|null
     */
    public function getResultInfo()
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
        foreach ($this->reflector->getParameters() as $p)
            $result[] = ParameterInfo::__internal_create($this, $p);
        return $result;
    }
}