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
     * 
     * @return Type
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * 
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }
}