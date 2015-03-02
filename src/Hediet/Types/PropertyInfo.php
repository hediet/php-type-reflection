<?php

namespace Hediet\Reflection;


class PropertyInfo
{
    /**
     * @var Type
     */
    private $declaringType;
    /**
     * @var \ReflectionProperty
     */
    private $reflectionProperty;


    /**
     * @var string
     */
    private $documentation;

    /**
     * @var Type
     */
    private $type;

    private $initialized = false;

    private function __construct(Type $declaringType, \ReflectionProperty $reflectionProperty)
    {
        $this->declaringType = $declaringType;
        $this->reflectionProperty = $reflectionProperty;
    }

    public function getName()
    {
        return $this->reflectionProperty->getName();
    }

    /**
     * @return Type
     */
    public function getDeclaringType()
    {
        return $this->declaringType;
    }

    /**
     * @return \ReflectionProperty
     */
    public function getReflectionProperty()
    {
        return $this->reflectionProperty;
    }


    private function initialize()
    {
        $this->initialized = true;
    }


    public function getDocumentation()
    {
        if (!$this->initialized)
            $this->initialize();
        return $this->documentation;
    }

    public function getType()
    {
        if (!$this->initialized)
            $this->initialize();
        return $this->type;
    }
}