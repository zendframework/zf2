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
     * @var int
     */
    protected $length;

    /**
     * @var string Change type to text
     */
    protected $type = 'TEXT';

    /**
     * @param null $name
     * @param null|int $length
     * @param bool $nullable
     */
    public function __construct($name, $length = null, $nullable = false)
    {
        $this->setName($name);
        $this->setLength($length);
        $this->setNullable($nullable);
    }

    /**
     * @param  int $length
     * @return self
     */
    public function setLength($length)
    {
        $this->length = $length;
        return $this;
    }

    /**
     * @return int
     */
    public function getLength()
    {
        return $this->length;
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

        // length
        if ($this->length) {
            $spec    .= '(%s)';
            $params[] = $this->length;
            $types[]  = self::TYPE_LITERAL;
        }

        // length
        if (!$this->isNullable) {
            $spec    .= ' %s';
            $params[] = 'NOT NULL';
            $types[]  = self::TYPE_LITERAL;
        }

        return array(array(
            $spec,
            $params,
            $types,
        ));
    }
}
