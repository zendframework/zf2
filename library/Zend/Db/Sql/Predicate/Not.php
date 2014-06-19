<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 6/19/14
 * Time: 11:44 AM
 */

namespace Zend\DB\Sql\Predicate;

class Not implements PredicateInterface
{
    /**
     * @var string
     */
    protected $specification = 'NOT (%1$s)';

    protected $expression;

    public function __construct($expression = null)
    {
        $this->expression = $expression;
    }

    /**
     * @param null $expression
     */
    public function setExpression($expression)
    {
        $this->expression = $expression;
    }

    /**
     * @return null
     */
    public function getExpression()
    {
        return $this->expression;
    }

    /**
     * @param  string $specification
     * @return self
     */
    public function setSpecification($specification)
    {
        $this->specification = $specification;
        return $this;
    }

    /**
     * @return string
     */
    public function getSpecification()
    {
        return $this->specification;
    }

    /**
     * @return array
     */
    public function getExpressionData()
    {
        return array(
            array($this->specification, array($this->expression), array(self::TYPE_VALUE))
        );
    }
} 