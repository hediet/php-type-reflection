<?php

namespace Hediet\Types;

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
        ksort($newTypes); //to ensure that $a->equals($b) ==> $a->getName() === $b->getName()
        //(string|object)|Foo => string|Foo
        $result = self::removeCoveredTypes($newTypes);
        
        if (count($result) === 1)
            return reset($result);
        
        return new UnionType($result);
    }
    
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
    
    private function __construct(array $types)
    {
        $this->types = $types;
    }

    public function getName()
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
     * @return Type[]
     */
    public function getTypes()
    {
        return $this->types;
    }
}
