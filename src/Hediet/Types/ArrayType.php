<?php
namespace Hediet\Types;

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
    
    public function getName()
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

    public function isAssignableFrom(Type $type)
    {
        if (!$type instanceof ArrayType)
            return false;
        if (!($type->itemType->isAssignableFrom($this->itemType) &&
              $this->itemType->isAssignableFrom($type->itemType)))
            return false;
        if (!($type->keyType->isAssignableFrom($this->keyType) &&
              $this->keyType->isAssignableFrom($type->keyType)))
            return false;
        
        return true;
    }

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
     * @return Type
     */
    public function getKeyType()
    {
        return $this->keyType;
    }
    
    /**
     * @return Type
     */
    public function getItemType()
    {
        return $this->itemType;
    }
}