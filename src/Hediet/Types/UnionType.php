<?php

namespace Hediet\Types;

/**
 * Represents a union of types.
 */
class UnionType extends Type
{
    /**
     * @param Type[] $types
     * @return Type
     */
    public static function __internal_create(array $types)
    {
        if (count($types) === 0)
            throw new \ReflectionException("Union type must contain at least one type.");
            
        //ensures that there are no nested union and duplicated types.
        $newTypes = array();
        foreach ($types as $type)
        {
            if ($type instanceof UnionType)
                $newTypes = array_merge($newTypes, $type->getTypes());
            else
                $newTypes[$type->getName()] = $type;
        }
        
        //to ensure that $a->equals($b) ==> $a->getName() === $b->getName()
        usort($newTypes, function(Type $a, Type $b) 
        {
            //order: non-primitives, primitives, null
            if ($a->equals(Type::ofNull()))
                return ($b->equals(Type::ofNull())) ? 0 : 1;
            else if ($b->equals(Type::ofNull()))
                return -1;
            
            if (($a instanceof PrimitiveType) === ($b instanceof PrimitiveType))
                return strcmp($a->getName(), $b->getName());
            
            return ($a instanceof PrimitiveType) ? 1 : -1;
        });
        
        //(object|string)|Foo => Foo|string
        $result = self::removeCoveredTypes($newTypes);
        
        if (count($result) === 1)
            return reset($result);
        
        return new UnionType($result);
    }
    
    /**
     * 
     * @param Type[] $types
     * @return Type[]
     */
    private static function removeCoveredTypes(array $types)
    {
        /* @var $result Type[] */
        $result = array();
        
        foreach ($types as $type)
        {
            $typeIsCoveredByExistingType = false;
            $typeCoversExistingType = false;
            
            foreach ($result as $key => $existingType)
            {
                if ($type->isAssignableFrom($existingType))
                {
                    unset($result[$key]);
                    $typeCoversExistingType = true;
                    break;
                }
                else if ($existingType->isAssignableFrom($type))
                {
                    $typeIsCoveredByExistingType = true;
                    break;
                }
            }

            if ($typeCoversExistingType)
            {
                foreach ($result as $key => $existingType)
                    if ($type->isAssignableFrom($existingType))
                        unset($result[$key]);
            }
            
            if (!$typeIsCoveredByExistingType)
                $result[] = $type;
        }
        
        return $result;
    }
    
    /**
     * @var Type[]
     */
    private $types;
    
    /**
     * 
     * @param Type[] $types
     */
    private function __construct(array $types)
    {
        $this->types = $types;
    }

    /**
     * Gets the name of the union type.
     * This name is canonical, so two equal union types have the same name.
     * 
     * @param array $options
     * @return string
     */
    public function getName(array $options = array())
    {
        $result = "";
        foreach ($this->getTypes() as $type)
        {
            if ($result !== "")
                $result .= "|";
            $result .= $type->getName();
        }
        return $result;
    }

    
    public function isAssignableFrom(Type $type)
    {
        if ($type instanceof UnionType)
        {
            //foreach subtype t of $type there must
            //be a subtype of $this which accepts t.
            //example: Foo|null is assignable to object|null,
            //But Foo|string|null is not assignable to object|string.
            
            foreach ($type->getTypes() as $subType)
            {
                if (!$this->isAssignableFrom($subType))
                    return false;
            }
            
            return true;
        }
        else
        {
            foreach ($this->getTypes() as $subType)
            {
                if ($subType->isAssignableFrom($type))
                    return true;
            }
            return false;
        }
    }

    public function isAssignableFromValue($value)
    {
        foreach ($this->getTypes() as $type)
        {
            if ($type->isAssignableFromValue($value))
                return true;
        }
        return false;
    }

    /**
     * Gets a list of the united types.
     * 
     * @return Type[]
     */
    public function getTypes()
    {
        return $this->types;
    }
}
