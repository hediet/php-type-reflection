<?php

namespace Hediet\Types;

use Hediet\Types\Helper\DocBlockParser;
use ReflectionProperty;

/**
 * Represents a property of a class.
 */
class PropertyInfo
{
    public static function __internal_create(ClassType $type, ReflectionProperty $reflector) 
    {
        return new PropertyInfo($type, $reflector);
    }    
    
    /**
     * @var ClassType
     */
    private $declaringType;
    
    /**
     * @var ReflectionProperty
     */
    private $reflector;

    /**
     * @var string
     */
    private $description;

    /**
     * @var Type
     */
    private $type;

    private $initialized = false;

    private function __construct(ClassType $declaringType, ReflectionProperty $reflector)
    {
        $this->declaringType = $declaringType;
        $this->reflector = $reflector;
    }

    /**
     * Gets the name of the property.
     * 
     * @return string
     */
    public function getName()
    {
        return $this->reflector->getName();
    }

    /**
     * Gets the class type type which declares this property.
     * 
     * @return ClassType
     */
    public function getDeclaringType()
    {
        return $this->declaringType;
    }

    /**
     * Gets the reflector for this property.
     * 
     * @return ReflectionProperty
     */
    public function getReflector()
    {
        return $this->reflector;
    }


    private function initialize()
    {
        $this->initialized = true;
        
        $parseResult = DocBlockParser::parsePropertyDocBlock($this->reflector);
        $this->description = $parseResult->description;
        
        $resolver = new Helper\ParserClassNameResolver($this->getDeclaringType()->getName());
        $this->type = Type::of($parseResult->type, $resolver);
    }

    /**
     * Gets the description of this property.
     * 
     * @return string
     */
    public function getDescription()
    {
        if (!$this->initialized)
            $this->initialize();
        return $this->description;
    }

    /**
     * Gets the type of this property.
     * 
     * @return Type
     */
    public function getType()
    {
        if (!$this->initialized)
            $this->initialize();
        return $this->type;
    }
}