<?php

namespace Hediet\Types;

/**
 * Represents a class.
 */
class ClassType extends ObjectType
{
    /**
     * @param string $className
     * @return ClassType
     */
    public static function __internal_create($className)
    {
        return new ClassType($className);
    }

    /**
     * @var string
     */
    private $className;

    /**
     * @param $className string
     */
    private function __construct($className)
    {
        $this->className = $className;
    }

    /**
     * @return PropertyInfo[]
     */
    public function getProperties()
    {
        //todo cache
        $result = array();
        foreach ($this->getReflectionClass()->getProperties() as $m)
        {
            $result[] = PropertyInfo::__internal_create($this, $m);
        }
        return $result;
    }

    /**
     * 
     * @param string $name
     * @return PropertyInfo
     */
    public function getProperty($name)
    {
        $m = $this->getReflectionClass()->getProperty($name);
        return PropertyInfo::__internal_create($this, $m);
    }

    /**
     * Gets the full qualified name of the class.
     * The name does not start with a backslash.
     *
     * @return string
     */
    public function getName(array $options = array())
    {
        return $this->className;
    }

    /**
     * Checks whether the provided type equals this type or is a subtype of this type.
     *
     * @param Type $type
     * @return boolean
     */
    public function isAssignableFrom(Type $type)
    {
        if ($type->getName() === $this->getName())
            return true;

        if (!($type instanceof ClassType))
            return false;

        return $type->isSubtypeOf($this);
    }

    /**
     * Checks whether the provided value is an instance of either this type or a subclass of this type.
     * 
     * @param mixed $value
     * @return boolean
     */
    public function isAssignableFromValue($value)
    {
        if (!is_object($value))
            return false;

        if (get_class($value) === $this->getName())
            return true;

        return is_subclass_of($value, $this->className);
    }

    /**
     * Checks whether the class implements the interface $type.
     * 
     * @param InterfaceType $type The interface.
     * @return boolean
     */
    public function isImplementorOf(InterfaceType $type)
    {
        return $this->getReflectionClass()->implementsInterface($type->getName());
    }

    /**
     * Gets all interfaces that were implemented by this class.
     * 
     * @return InterfaceType[]
     */
    public function getImplementedInterfaces()
    {
        $result = array();
        foreach ($this->getReflectionClass()->getInterfaces() as $interface)
        {
            $result[] = Type::byReflectionClass($interface);
        }
        return $result;
    }

}
