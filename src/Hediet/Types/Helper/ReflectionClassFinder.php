<?php

namespace Hediet\Types\Helper;

use Doctrine\Common\Reflection\ClassFinderInterface;
use ReflectionClass;

class ReflectionClassFinder implements ClassFinderInterface
{
    public function findFile($class)
    {
        $c = new ReflectionClass($class);
        return $c->getFileName();
    }
}