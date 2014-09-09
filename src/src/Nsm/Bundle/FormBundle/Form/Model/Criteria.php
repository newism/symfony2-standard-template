<?php

namespace Nsm\Bundle\FormBundle\Form\Model;

class Criteria
{

    const EXPRESSION_EQ        = 'eq';
    const EXPRESSION_NEQ       = 'neq';
    const EXPRESSION_LT        = 'lt';
    const EXPRESSION_LTE       = 'lte';
    const EXPRESSION_GT        = 'gt';
    const EXPRESSION_GTE       = 'gte';
    const EXPRESSION_BTW       = 'btw';
    const EXPRESSION_BTWE      = 'btwe';
    const EXPRESSION_LIKE      = 'like';
    const EXPRESSION_NLIKE     = 'nlike';
    const EXPRESSION_RLIKE     = 'rlike';
    const EXPRESSION_LLIKE     = 'llike';
    const EXPRESSION_ISNULL    = 'isNull';
    const EXPRESSION_ISNOTNULL = 'isNotNull';

    /**
     * Get all the available expressions for this criteria type
     * @return array
     */
    public static function getExpressions()
    {
        return array(
            self::EXPRESSION_BTW,
            self::EXPRESSION_EQ,
            self::EXPRESSION_NEQ,
            self::EXPRESSION_LT,
            self::EXPRESSION_LTE,
            self::EXPRESSION_GT,
            self::EXPRESSION_GTE,
            self::EXPRESSION_BTW,
            self::EXPRESSION_BTWE,
            self::EXPRESSION_LIKE,
            self::EXPRESSION_NLIKE,
            self::EXPRESSION_RLIKE,
            self::EXPRESSION_LLIKE,
            self::EXPRESSION_ISNULL,
            self::EXPRESSION_ISNOTNULL
        );
    }

    /**
     * @var string
     */
    protected $attribute;

    /**
     * @var string
     */
    protected $expression;

    /**
     * @param $attribute
     * @param $expression
     */
    public function __construct($attribute, $expression)
    {
        $this->attribute  = $attribute;
        $this->expression = $expression;
    }

    /**
     * @param string $attribute
     */
    public function setAttribute($attribute)
    {
        $this->attribute = $attribute;
    }

    /**
     * @return string
     */
    public function getAttribute()
    {
        return $this->attribute;
    }

    /**
     * @param string $expression
     */
    public function setExpression($expression)
    {
        $this->expression = $expression;
    }

    /**
     * @return string
     */
    public function getExpression()
    {
        return $this->expression;
    }
}
