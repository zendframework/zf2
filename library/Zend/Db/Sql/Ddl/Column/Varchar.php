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
 * Class Varchar: add null, default specifying
 * @package Zend\Db\Sql\Ddl\Column
 */
class Varchar extends Column
{
    /**
     * @var int
     */
    protected $length;

    /**
     * @var string
     */
    protected $specification = '%s VARCHAR(%s) %s';

    /**
     * @param null|string $name
     * @param int $length
     * @param bool $nullable
     * @param null $default
     */
    public function __construct($name, $length, $nullable = false, $default = null)
    {
        $this->name   = $name;
        $this->length = $length;
        $this->setNullable($nullable);
        $this->setDefault($default);
    }

    /**
     * @return array
     */
    public function getExpressionData()
    {
        $spec   = $this->specification;
        $params = array();

        $types    = array(self::TYPE_IDENTIFIER, self::TYPE_LITERAL);
        $params[] = $this->name;
        $params[] = $this->length;

        $types[]  = self::TYPE_LITERAL;
        $params[] = (!$this->isNullable) ? 'NOT NULL ' : '';

        if ($this->default !== null) {
            // have space after not null for backwards test compatibility
            $spec    .= 'DEFAULT %s';
            $params[] = $this->default;
            $types[]  = self::TYPE_VALUE;
        }

        return array(array(
            $spec,
            $params,
            $types,
        ));
    }
}
