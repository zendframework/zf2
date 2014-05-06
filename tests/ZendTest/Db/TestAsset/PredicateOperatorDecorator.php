<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Db\TestAsset;

use Zend\Db\Sql\Predicate\Operator;
use Zend\Db\Sql\Platform\PlatformDecoratorInterface;

class PredicateOperatorDecorator extends Operator implements PlatformDecoratorInterface
{

    protected $subject = null;

    public function getExpressionData()
    {
        // localize variables
        foreach (get_object_vars($this->subject) as $name => $value) {
            $this->{$name} = $value;
        }
        return array(array(
            '{%s ' . $this->operator . ' %s}',
            array($this->left, $this->right),
            array($this->leftType, $this->rightType)
        ));
    }

    public function setSubject($subject)
    {
        $this->subject = $subject;
        return $this;
    }

    public function hasSubject()
    {
        return $this->subject != null;
    }
}
