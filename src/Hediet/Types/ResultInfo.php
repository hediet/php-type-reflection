<?php

namespace Hediet\Types;

class ResultInfo
{

    private $type;
    private $description;

    public function __construct($description, Type $type)
    {
        $this->description = $description;
        $this->type = $type;
    }

    /**
     * Gets the type of the result.
     * 
     * @return Type
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Gets the description of the result.
     * 
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }
}