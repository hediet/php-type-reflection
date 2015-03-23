<?php

namespace Hediet\Types\Helper;

use Doctrine\Common\Reflection\StaticReflectionParser;
use Hediet\Types\RelativeClassNameResolver;

class ParserClassNameResolver implements RelativeClassNameResolver
{
    /**
     * @var string
     */
    private $className;

    /**
     * @var string[string] the key must be lowercase
     */
    private $useStatements;
    
    /**
     * @var string
     */
    private $namespace;

    public function __construct($className)
    {
        $this->className = $className;
    }
    
    public function resolveRelativeName($shortClassName)
    {
        if ($this->useStatements === null)
        {
            $p = new StaticReflectionParser($this->className, new ReflectionClassFinder());
            
            $this->useStatements = $p->getUseStatements();
            $this->namespace = $p->getNamespaceName();
        }

        $matches = array();
        preg_match("/^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*/", $shortClassName, $matches);
        $firstName = strtolower($matches[0]);

        if (isset($this->useStatements[$firstName]))
            return $this->useStatements[$firstName] . substr($shortClassName, strlen($firstName));
        else
            return $this->namespace . "\\" . $shortClassName;
    }
}
