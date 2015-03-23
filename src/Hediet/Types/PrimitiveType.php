<?php

namespace Hediet\Types;

use Exception;

/**
 * Represents a primitive type
 * This can be either an integer, float, string, 
 * boolean, mixed, resource, object, null or callable.
 */
class PrimitiveType extends Type
{
    const INTEGER_NAME = "integer";
    const FLOAT_NAME = "float";
    const STRING_NAME = "string";
    const BOOLEAN_NAME = "boolean";
    const MIXED_NAME = "mixed";
    const RESOURCE_NAME = "resource";
    const OBJECT_NAME = "object";
    const NULL_NAME = "null";
    const CALLABLE_NAME = "callable";

    private static $cachedTypes;

    /**
     * Parses a primitive type name.
     * Returns null, if the provided type name is not a name of a primitive type.
     * @param string $typeName
     * @return PrimitiveType|null
     */
    public static function parse($typeName)
    {
        $normalize = array("int" => self::INTEGER_NAME, 
            "bool" => self::BOOLEAN_NAME, 
            "double" => self::FLOAT_NAME,
            "real" => self::FLOAT_NAME,
            "long" => self::INTEGER_NAME);

        if (isset($normalize[$typeName]))
            $typeName = $normalize[$typeName];

        if (self::$cachedTypes === null)
        {
            self::$cachedTypes = array(
                self::INTEGER_NAME => new PrimitiveType(self::INTEGER_NAME),
                self::FLOAT_NAME => new PrimitiveType(self::FLOAT_NAME),
                self::STRING_NAME => new PrimitiveType(self::STRING_NAME),
                self::BOOLEAN_NAME => new PrimitiveType(self::BOOLEAN_NAME),
                self::MIXED_NAME => new PrimitiveType(self::MIXED_NAME),
                self::RESOURCE_NAME => new PrimitiveType(self::RESOURCE_NAME),
                self::OBJECT_NAME => new PrimitiveType(self::OBJECT_NAME),
                self::NULL_NAME => new PrimitiveType(self::NULL_NAME),
                self::CALLABLE_NAME => new PrimitiveType(self::CALLABLE_NAME));
        }

        if (isset(self::$cachedTypes[$typeName]))
            return self::$cachedTypes[$typeName];

        return null;
    }

    /**
     * @var string
     */
    private $name;

    /**
     * @param string $name
     */
    private function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * Gets the name of the primitive type.
     * 
     * @param array $options
     * @return string
     */
    public function getName(array $options = array())
    {
        return $this->name;
    }

    /**
     * Checks whether the provided type is assignable to this type.
     * Returns true, if the provided type is equal to this instance, or
     * if this instance is "mixed", or if this is "object" and type is an object type
     * or if this is "callable" and type is a class with a method __invoke.
     * 
     * @param Type $type
     * @return boolean
     */
    public function isAssignableFrom(Type $type)
    {
        if ($type->getName() === $this->name)
            return true;
        if ($this->name === self::MIXED_NAME)
            return true;
        if ($this->name === self::OBJECT_NAME)
            return ($type instanceof ObjectType);
        if ($this->name === self::CALLABLE_NAME)
        {
            if ($type instanceof ObjectType)
                return $type->getReflectionClass()->hasMethod("__invoke");
        }
        return false;
    }

    /**
     * Checks whether the value is assingable to this primitive type.
     * Internally, the corresponding is_* method is called.
     * If this type is mixed, true will be returned regardless of the provided value.
     * 
     * @param mixed $value
     * @return boolean
     */
    public function isAssignableFromValue($value)
    {
        switch ($this->name)
        {
            case self::INTEGER_NAME:
                return is_int($value);
            case self::FLOAT_NAME:
                return is_float($value);
            case self::STRING_NAME:
                return is_string($value);
            case self::BOOLEAN_NAME:
                return is_bool($value);
            case self::MIXED_NAME:
                return true;
            case self::RESOURCE_NAME:
                return is_resource($value);
            case self::OBJECT_NAME:
                return is_object($value);
            case self::NULL_NAME:
                return is_null($value);
            case self::CALLABLE_NAME:
                return is_callable($value);
            default:
                throw new Exception("Implementation error");
        }
    }
}
