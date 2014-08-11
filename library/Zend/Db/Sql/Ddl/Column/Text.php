<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Db\Sql\Ddl\Column;


/**
 * Fix Text definitions: (as per http://dev.mysql.com/doc/refman/5.0/en/blob.html)
 * text can not have length nor default value
 *
 * @package Zend\Db\Sql\Ddl\Column
 */
class Text extends Column
{

    /**
     * @var string Change type to text
     */
    protected $type = 'TEXT';

    /**
     * @param null  $name
     * @param bool  $nullable
     */
    public function __construct($name, $nullable = false)
    {
        $this->setName($name);
        $this->setNullable($nullable);
    }

    /**
     * @return array
     */
    public function getExpressionData()
    {
        $spec = $this->specification;

        $params   = array();
        $params[] = $this->name;
        $params[] = $this->type;

        $types = array(self::TYPE_IDENTIFIER, self::TYPE_LITERAL);

        if (!$this->isNullable) {
            $params[1] .= ' NOT NULL';
        }

        return array(array(
            $spec,
            $params,
            $types,
        ));
    }
}
