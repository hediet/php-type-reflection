<?php
namespace Hediet\Types;

class ClassType extends ObjectType
{
    public static function __internal_create($className)
    {
        return new ClassType($className);
    }


    private $className;

    /**
     * @param $className string
     */
    private function __construct($className)
    {
        $this->className = $className;
    }

/* TODO
    public function getProperties()
    {

    }

    public function getProperty($name)
    {

    }
*/
    
    /**
     * Gets the name of the type.
     *
     * @return string
     */
    public function getName()
    {
        return $this->className;
    }

    /**
     * Checks whether values with the provided type can be assigned
     * to this type.
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

        return $this->getReflectionClass()->isSubclassOf($type->getName());
    }

    /**
     * Checks whether the provided value can be assigned to this type.
     * @param $value
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
}