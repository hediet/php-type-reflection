<?php

namespace Hediet\Types;

use ReflectionClass;
use ReflectionException;

abstract class Type
{
    // <editor-fold defaultstate="collapsed" desc="of-Primitive">

    /**
     * Gets the type of integer.
     * 
     * @return PrimitiveType
     */
    public static function ofInteger()
    {
        return PrimitiveType::parse(PrimitiveType::INTEGER_NAME);
    }

    /**
     * Gets the type of float.
     * 
     * @return PrimitiveType
     */
    public static function ofFloat()
    {
        return PrimitiveType::parse(PrimitiveType::FLOAT_NAME);
    }

    /**
     * Gets the type of string.
     * 
     * @return PrimitiveType
     */
    public static function ofString()
    {
        return PrimitiveType::parse(PrimitiveType::STRING_NAME);
    }

    /**
     * Gets the type of boolean.
     * 
     * @return PrimitiveType
     */
    public static function ofBoolean()
    {
        return PrimitiveType::parse(PrimitiveType::BOOLEAN_NAME);
    }

    /**
     * Gets the type of mixed.
     * 
     * @return PrimitiveType
     */
    public static function ofMixed()
    {
        return PrimitiveType::parse(PrimitiveType::MIXED_NAME);
    }

    /**
     * Gets the type of resource.
     * 
     * @return PrimitiveType
     */
    public static function ofResource()
    {
        return PrimitiveType::parse(PrimitiveType::RESOURCE_NAME);
    }

    /**
     * Gets the type of object.
     * 
     * @return PrimitiveType
     */
    public static function ofObject()
    {
        return PrimitiveType::parse(PrimitiveType::OBJECT_NAME);
    }

    // </editor-fold>

    /**
     * Gets an array type which elements are of type $itemType.
     * 
     * @param Type $itemType The type of the elements.
     * @return ArrayType
     */
    public static function ofArray(Type $itemType)
    {
        return ArrayType::__internal_create(Type::ofMixed(), $itemType);
    }

    /**
     * Gets a class or interface type with a given name.
     * 
     * @param string $fullName The name of the class or interface.
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
     * Gets a class or interface type by a reflection class.
     * 
     * @param ReflectionClass $reflectionClass The reflection class.
     * @return ObjectType
     */
    public static function byReflectionClass(ReflectionClass $reflectionClass)
    {
        return self::ofObjectType($reflectionClass->getName());
    }

    /**
     * Gets the type with the provided name.
     * Throws an exception if the type is malformed or does not exist.
     *
     * @param string $typeName The type name. Must include the full namespace if resolver is not set.
     * If resolver is set, absolute class or interface names must start with "\".
     * @param RelativeClassNameResolver $resolver The resolver which resolves relative type names to absolute ones.
     * If the resolver is not provided, relative names are assumed as absolute.
     */
    public static function of($typeName, RelativeClassNameResolver $resolver = null)
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
                $realTypeName = $resolver->resolveRelativeName($typeName);
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
     * @param Type $type The provided type.
     * @return boolean
     */
    public abstract function isAssignableFrom(Type $type);

    /**
     * Checks whether the provided value can be assigned to this type.
     * @param $value The provided value.
     * @return boolean
     */
    public abstract function isAssignableFromValue($value);
}
