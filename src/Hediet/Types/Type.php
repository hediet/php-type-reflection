<?php

namespace Hediet\Types;

use InvalidArgumentException;
use ReflectionClass;
use ReflectionException;

/**
 * Represents a php type.
 */
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
    
    /**
     * Gets the type of null.
     * 
     * @return PrimitiveType
     */
    public static function ofNull()
    {
        return PrimitiveType::parse(PrimitiveType::NULL_NAME);
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
            throw new InvalidArgumentException("Argument 'fullName' does not describe a class or interface.");
        return $result;
    }
    
    /**
     * Gets a class type with a given name.
     * 
     * @param string $fullName The name of the class.
     * @return ClassType
     */
    public static function ofClass($fullName)
    {
        $result = self::of($fullName);
        if (!($result instanceof ClassType))
            throw new InvalidArgumentException("Argument 'fullName' does not describe a class.");
        return $result;
    }
    
    /**
     * Gets a interface type with a given name.
     * 
     * @param string $fullName The name of the interface.
     * @return InterfaceType
     */
    public static function ofInterface($fullName)
    {
        $result = self::of($fullName);
        if (!($result instanceof InterfaceType))
            throw new InvalidArgumentException("Argument 'fullName' does not describe an interface.");
        return $result;
    }
    
    /**
     * Gets a type which unites the given types.
     * @param Type[] $types The types to unite.
     * @return Type The united type. Need not to be a UnionType, if it would unite only a single type.
     */
    public static function ofUnion(array $types)
    {
        return UnionType::__internal_create($types);
    }
    
    /**
     * Gets a type that allows the given type to be null.
     * 
     * @param \Hediet\Types\Type $type
     */
    public static function ofNullable(Type $type)
    {
        return self::ofUnion(array($type, Type::ofNull()));
    }
    
    /**
     * 
     * @param mixed $value
     * @return Type
     */
    public static function byValue($value)
    {
        if (is_object($value))
            return self::of(get_class($value));
        else if (is_array($value))
        {
            //todo: find most common type of items
            return self::ofArray(self::ofMixed());
        }
        else if (is_string($value))
            return self::ofString();
        else if (is_bool($value))
            return self::ofBoolean();
        else if (is_resource($value))
            return self::ofResource();
        else if (is_int($value))
            return self::ofInteger();
        else if (is_float($value))
            return self::ofFloat();
        else if ($value === null)
            return self::of("null"); //TODO
        
        throw new \Exception("Each value must have a type.");
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
        $parts = explode("|", $typeName);
        if (count($parts) > 1)
        {
            $types = array();
            foreach ($parts as $part)
                $types[] = self::of($part, $resolver);
            return self::ofUnion($types);
        }
        
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
                throw new ReflectionException("Type '" . $realTypeName . "' does not exist.");

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
    public abstract function getName(array $options = array());

    /**
     * Gets the string representation of the type.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getName(array());
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
     * 
     * @param mixed $value The provided value.
     * @return boolean
     */
    public abstract function isAssignableFromValue($value);
    
    /**
     * Checks whether the type is equal to $other.
     * 
     * @param Type $other The other type.
     * @return boolean
     */
    public function equals(Type $other)
    {
        $result = $other->isAssignableFrom($this) && $this->isAssignableFrom($other);
        $result2 = $this->getName() === $other->getName();
        
        //Are there any types which do not have the same name, but are assignable to each other?
        //(United types are sorted by their names)
        if ($result !== $result2)
            throw new \Exception("This should not happen - please report a bug!");
        
        return $result;
    }
}
