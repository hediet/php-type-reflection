<?php

namespace Hediet\Types\Helper;

use Hediet\Types\Helper\ResultInfo;
use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\DocBlock\Tag\ReturnTag;
use phpDocumentor\Reflection\DocBlock\Tag;
use ReflectionMethod;
use ReflectionProperty;

class DocBlockParser
{

    public static function parseMethodDocBlock(ReflectionMethod $reflectionMethod)
    {
        $phpdoc = new DocBlock($reflectionMethod->getDocComment());

        /* @var $paramTags Tag\ParamTag[] */
        $paramTags = $phpdoc->getTagsByName("param");
        $returnTags = $phpdoc->getTagsByName("return");

        /* @var $returnTag ReturnTag */
        if (count($returnTags) > 0)
            $returnTag = $returnTags[0];
        else
            $returnTag = null;

        $result = new ParseMethodBlockResult();
        $result->description = $phpdoc->getShortDescription();

        if ($returnTag !== null)
        {
            $result->resultInfo = new ResultInfo();
            $result->resultInfo->description = $returnTag->getDescription();
            $result->resultInfo->type = (string)$returnTag->getType();
        }

        $result->parameter = array();

        foreach ($paramTags as $p)
        {
            $param = new ParseMethodBlockResult_Parameter();
            $variableName = substr($p->getVariableName(), 1);
            $param->name = $variableName;
            $param->description= $p->getDescription();
            $param->type = (string)$p->getType();
            $result->parameter[$variableName] = $param;
        }

        return $result;
    }

    public function parsePropertyDocBlock(ReflectionProperty $reflectionProperty)
    {
        //todo
    }

}

class ParseMethodBlockResult
{
    /**
     * @var string
     */
    public $description;
    
    /**
     * @var ResultInfo
     */
    public $resultInfo;

    /**
     * @var ParseMethodBlockResult_Parameter[string]
     */
    public $parameter;

}
class ParseMethodBlockResult_Parameter
{
    /**
     *
     * @var string
     */
    public $name;
    
    /**
     *
     * @var string
     */
    public $type;
    
    /**
     *
     * @var string
     */
    public $description;
}


namespace phpDocumentor\Reflection\DocBlock\Type;

//To prevent the original collection from expanding the type name.
class Collection extends \ArrayObject
{
    private $type;
    
    public function __construct(
        array $types = array(),
        Context $context = null
    ) {
        $this->type = implode("|", $types);
    }


    public function __toString()
    {
        return $this->type;
    }
}
