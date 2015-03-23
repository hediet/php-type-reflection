<?php

namespace Hediet\Types;

/**
 * Represents an interface.
 */
class InterfaceType extends ObjectType
{
    public static function __internal_create($interfaceName)
    {
        return new InterfaceType($interfaceName);
    }

    
    /**
     * @var string
     */
    private $interfaceName;

    /**
     * @param $interfaceName string
     */
    private function __construct($interfaceName)
    {
        $this->interfaceName = $interfaceName;
    }

    /**
     * Gets the name of the type.
     *
     * @return string
     */
    public function getName(array $options = array())
    {
        return $this->interfaceName;
    }

    /**
     * Checks whether the provided type is either an interface that is equal to or extends this interface
     * or is a class that implements this interface.
     *
     * @param Type $type
     * @return boolean
     */
    public function isAssignableFrom(Type $type)
    {
        if ($type->getName() === $this->getName())
            return true;

        if ($type instanceof InterfaceType)
        {
            return $type->isSubtypeOf($this);
        }
        else if ($type instanceof ClassType)
        {
            return $type->isImplementorOf($this);
        }
        return false;
    }

    /**
     * Checks whether the provided value is an object that implements this interface.
     *
     * @param $value
     * @return boolean
     */
    public function isAssignableFromValue($value)
    {
        return is_object($value) && in_array($this->getName(), class_implements($value));
    }
}