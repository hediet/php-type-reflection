<?php

namespace Hediet\Types;

use Exception;

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

    public static function parse($typeName)
    {
        $normalize = array("int" => self::INTEGER_NAME, 
            "bool" => self::BOOLEAN_NAME, "double" => self::FLOAT_NAME);

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

    private $name;

    private function __construct($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

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
