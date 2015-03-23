<?php

namespace Hediet\Types;

//TODO this class is currently not supported

class GenericParameterType extends Type
{
    public static function __internal_create($name, Type $declaringType)
    {
        return new GenericParameterType($name, $declaringType);
    }
    
    /**
     * @var Type
     */
    private $declaringType;
    
    /**
     * @var string
     */
    private $name;
    
    private function __construct($name, Type $declaringType)
    {
        $this->name = $name;
        $this->declaringType = $declaringType;
    }

    public function getIsCovariant()
    {
        return false;
    }

    public function getIsContravariant()
    {
        return false;
    }

    public function getIsInvariant()
    {
        return !$this->getIsCovariant() && !$this->getIsContravariant();
    }
    

    public function getName(array $options = array())
    {
        return $this->name;
    }

    public function isAssignableFrom(Type $type)
    {
        if (!$type instanceof GenericParameterType)
            return false;
        
        return $type->name === $this->name;
    }

    public function isAssignableFromValue($value)
    {
        return false;
    }

}