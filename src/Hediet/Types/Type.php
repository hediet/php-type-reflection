<?php

namespace Hediet\Types;

use ReflectionClass;
use ReflectionException;

abstract class Type
{

    // <editor-fold defaultstate="collapsed" desc="of-Primitive">

    /**
     * @return PrimitiveType
     */
    public static function ofInteger()
    {
        return PrimitiveType::parse(PrimitiveType::INTEGER_NAME);
    }

    /**
     * @return PrimitiveType
     */
    public static function ofFloat()
    {
        return PrimitiveType::parse(PrimitiveType::FLOAT_NAME);
    }

    /**
     * @return PrimitiveType
     */
    public static function ofString()
    {
        return PrimitiveType::parse(PrimitiveType::STRING_NAME);
    }

    /**
     * @return PrimitiveType
     */
    public static function ofBoolean()
    {
        return PrimitiveType::parse(PrimitiveType::BOOLEAN_NAME);
    }

    /**
     * @return PrimitiveType
     */
    public static function ofMixed()
    {
        return PrimitiveType::parse(PrimitiveType::MIXED_NAME);
    }

    /**
     * @return PrimitiveType
     */
    public static function ofResource()
    {
        return PrimitiveType::parse(PrimitiveType::RESOURCE_NAME);
    }

    /**
     * @return PrimitiveType
     */
    public static function ofObject()
    {
        return PrimitiveType::parse(PrimitiveType::OBJECT_NAME);
    }

    // </editor-fold>

    /**
     * 
     * @param Type $itemType
     * @return ArrayType
     */
    public static function ofArray(Type $itemType)
    {
        return ArrayType::__internal_create(Type::ofMixed(), $itemType);
    }
    
    /**
     * @param string $fullName
     * @return ObjectType
     */
    public static function ofObjectType($fullName)
    {
        $result = self::of($fullName);
        if (!($result instanceof ObjectType))
            throw new \InvalidArgumentException("Argument 'fullName' does not describe a class or interface.");
        return $result;
    }
    
    /**
     * @param ReflectionClass $reflectionClass
     * @return ObjectType
     */
    public static function byReflectionClass(ReflectionClass $reflectionClass)
    {
        return self::ofObjectType($reflectionClass->getName());
    }
    
    
    /**
     * Gets the type with the provided name.
     * Throws an exception if the type is malformed.
     *
     * @param $typeName string the type name. Must include the full namespace if resolver is not set.
     * If resolver is set, absolute class or interface names must start with "\".
     */
    public static function of($typeName, ShortClassNameResolver $resolver = null)
    {
        $result = PrimitiveType::parse($typeName);

        if ($result == null)
        {
            if (substr($typeName, -2) === "[]")
            {
                $itemType = self::of(substr($typeName, 0, -2), $resolver);
                return self::ofArray($itemType);
            }
            
            if ($typeName === "array")
            {
                return self::ofArray(self::ofMixed());
            }

            $realTypeName = "";
            if (substr($typeName, 0, 1) === "\\")
            {
                $realTypeName = substr($typeName, 1);
            }
            else if ($resolver === null)
            {
                $realTypeName = $typeName;
            }
            else
            {
                $realTypeName = $resolver->resolveShortClassName($typeName);
            }
            
            $isInterface = interface_exists($realTypeName);
            
            if (!($isInterface || class_exists($realTypeName)))
                throw new ReflectionException("Class or Interface '" . $realTypeName . "' does not exist.");
            
            if ($isInterface)
                return InterfaceType::__internal_create($realTypeName);
            else
                return ClassType::__internal_create($realTypeName);
        }

        return $result;
    }

    /**
     * Gets the name of the type.
     *
     * @return string
     */
    public abstract function getName();

    /**
     * Gets the string representation of the type.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getName();
    }

    /**
     * Checks whether values with the provided type can be assigned
     * to this type.
     *
     * @param Type $type
     * @return boolean
     */
    public abstract function isAssignableFrom(Type $type);

    /**
     * Checks whether the provided value can be assigned to this type.
     * @param $value
     * @return boolean
     */
    public abstract function isAssignableFromValue($value);
}
