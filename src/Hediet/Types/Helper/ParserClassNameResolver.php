<?php

namespace Hediet\Types\Helper;

use Doctrine\Common\Reflection\StaticReflectionParser;
use Hediet\Types\MethodInfo;
use Hediet\Types\RelativeClassNameResolver;

class ParserClassNameResolver implements RelativeClassNameResolver
{
    /**
     * @var MethodInfo
     */
    private $methodInfo;

    /**
     * @var string[string] the key must be lowercase
     */
    private $useStatements;
    
    /**
     * @var string
     */
    private $namespace;

    public function __construct(MethodInfo $methodInfo)
    {
        $this->methodInfo = $methodInfo;
    }
    
    public function resolveRelativeName($shortClassName)
    {
        if ($this->useStatements === null)
        {
            $p = new StaticReflectionParser($this->methodInfo->getDeclaringType()->getName(), new ReflectionClassFinder());

            $m = $p->getReflectionMethod($this->methodInfo->getName());

            $this->useStatements = $m->getUseStatements();
            $this->namespace = $m->getNamespaceName();
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
