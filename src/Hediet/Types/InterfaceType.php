<?php

namespace Hediet\Types;

class InterfaceType extends ObjectType
{
    public static function __internal_create($interfaceName)
    {
        return new InterfaceType($interfaceName);
    }


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
    public function getName()
    {
        return $this->interfaceName;
    }

    /**
     * Checks whether values with the provided type can be assigned
     * to this interface.
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
            return $type->getReflectionClass()->isSubclassOf($this->getName());
        }
        else if ($type instanceof ClassType)
        {
            return $type->getReflectionClass()->implementsInterface($this->getName());
        }
        return false;
    }

    /**
     * Checks whether the provided value can be assigned to this interface.
     *
     * @param $value
     * @return boolean
     */
    public function isAssignableFromValue($value)
    {
        return is_object($value) && in_array($this->getName(), class_implements($value));
    }
}