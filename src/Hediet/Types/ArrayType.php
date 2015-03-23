<?php

namespace Hediet\Types;

/**
 * Represents an array with a key and item type.
 * By default the key and item type are mixed.
 */
class ArrayType extends Type
{
    public static function __internal_create(Type $keyType, Type $itemType)
    {
        return new ArrayType($keyType, $itemType);
    }
    
    /**
     * @var Type
     */
    private $keyType;
    
    /**
     * @var Type
     */
    private $itemType;

    private function __construct(Type $keyType, Type $itemType)
    {
        $this->keyType = $keyType;
        $this->itemType = $itemType;
    }
    
    /**
     * Gets the string representation of this type.
     * If key and value are mixed, "array" is returned.
     * If only key is mixed, "T[]" is returned, where T is the name of the value type.
     * Otherwise, "array<K, V>" is returned, where K is the name of the key type.
     * 
     * @return string The string representation.
     */
    public function getName(array $options = array())
    {
        if ($this->keyType === Type::ofMixed())
        {
            if ($this->itemType === Type::ofMixed())
                return "array";
            
            return $this->itemType->getName() . "[]";
        }
        
        return "array<" . $this->keyType->getName() 
                . "," . $this->itemType->getName() . ">"; 
    }

    /**
     * Checks whether the provided type represents
     * an array whose key and value type are equal to the key and value type of this instance.
     *
     * @param Type $type The provided type.
     * @return boolean
     */
    public function isAssignableFrom(Type $type)
    {
        if (!$type instanceof ArrayType)
            return false;
        if (!$type->itemType->equals($this->itemType))
            return false;
        if (!$type->keyType->equals($this->keyType))
            return false;
        
        return true;
    }

    /**
     * Checks whether the provided value is an array and all keys and values
     * are assignable to the key and value type of this instance.
     * 
     * @param mixed $value The provided value.
     * @return boolean
     */
    public function isAssignableFromValue($value)
    {
        if (!is_array($value))
            return false;
        foreach ($value as $key => $item)
        {
            if (!$this->itemType->isAssignableFromValue($item))
                return false;
            if (!$this->keyType->isAssignableFromValue($key))
                return false;
        }
        return true;
    }

    /**
     * Gets the key type.
     * 
     * @return Type
     */
    public function getKeyType()
    {
        return $this->keyType;
    }
    
    /**
     * Gets the item type.
     * 
     * @return Type
     */
    public function getItemType()
    {
        return $this->itemType;
    }
}