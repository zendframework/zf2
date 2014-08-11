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
 * Fix Blob definitions: (as per http://dev.mysql.com/doc/refman/5.0/en/blob.html)
 * blob can not have length nor default value
 *
 * @package Zend\Db\Sql\Ddl\Column
 */
class Blob extends Column
{
    /**
     * @var int
     */
    protected $length;

    /**
     * @var string Change type to blob
     */
    protected $type = 'BLOB';

    /**
     * Some of the parameters won't really taking part in expression (as of 2.4.X):
     * default and options
     * left for BC
     * 
     * @param null  $name
     * @param int|null $length
     * @param bool  $nullable
     * @param null|string $default
     * @param array $options
     */
    public function __construct($name, $length = null, $nullable = false, $default = null, array $options = array())
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
