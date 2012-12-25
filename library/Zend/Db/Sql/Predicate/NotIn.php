<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Db
 */

namespace Zend\Db\Sql\Predicate;

use Zend\Db\Sql\Select;

/**
 * @category   Zend
 * @package    Zend_Db
 * @subpackage Sql
 */
class NotIn extends In
{
    /**
     * Return array of parts for where statement
     *
     * @return array
     */
    public function getExpressionData()
    {
        $values = $this->getValueSet();
        if ($values instanceof Select) {
            $specification = '%s NOT IN %s';
            $types = array(self::TYPE_VALUE);
            $values = array($values);
        } else {
            $specification = '%s NOT IN (' . implode(', ', array_fill(0, count($values), '%s')) . ')';
            $types = array_fill(0, count($values), self::TYPE_VALUE);
        }

        $identifier = $this->getIdentifier();
        array_unshift($values, $identifier);
        array_unshift($types, self::TYPE_IDENTIFIER);

        return array(array(
            $specification,
            $values,
            $types,
        ));
    }

}
