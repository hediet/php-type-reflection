<?php

/**
 * @author Henning Dieterichs <henning.dieterichs@hediet.de>
 * @copyright (c) 2013-2014, Henning Dieterichs <henning.dieterichs@hediet.de>
 * @license http://opensource.org/licenses/MIT MIT
 */

namespace Hediet;

use Hediet\Types\Type;
use PHPUnit_Framework_TestCase;

class Foo
{
    /**
     * Bla
     * @ApiRoute("test")
     * @param Type $arg
     * @return Type the type
     */
    public function test($arg)
    {

    }
}


class Test extends PHPUnit_Framework_TestCase
{
    public function test1()
    {
        $m = Type::ofObjectType("Hediet\Foo")->getMethod("test");
                
        $m->getReflectionMethod();
        
        $t = $m->getParameters()[0]->getType();
        
        if ($t instanceof Types\ObjectType)
        {
            foreach ($t->getMethods() as $m)
                echo $m->getDescription();
        }
        
        print_r($t);
        
        
       //echo Type::ofArray(Type::of("mixed"));
    }
    

}
