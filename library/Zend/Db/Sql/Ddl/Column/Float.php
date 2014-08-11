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
 * Class Float add zerofill, unsigned attributes
 * coming in options array
 * @package Zend\Db\Sql\Ddl\Column
 */
class Float extends Column
{
    /**
     * @var int
     */
    protected $decimal;

    /**
     * @var int
     */
    protected $digits;

    /**
     * @var string
     */
    protected $specification = '%s DECIMAL(%s) %s %s';

    /**
     * @param null|string $name
     * @param int $digits
     * @param int $decimal
     * @param array|null $options
     */
    public function __construct($name, $digits, $decimal, array $options = null)
    {
        $this->name    = $name;
        $this->digits  = $digits;
        $this->decimal = $decimal;
        if (is_null($options)) {
            $options = array();
        }
        $this->setOptions($options);
    }

    /**
     * @return array
     */
    public function getExpressionData()
    {
        $spec   = $this->specification;
        $params = array();
        $options = $this->getOptions();

        $types      = array(self::TYPE_IDENTIFIER, self::TYPE_LITERAL);
        $params[]   = $this->name;
        $params[]   = $this->digits;
        $params[1] .= ', ' . $this->decimal;

        if (isset($options['zerofill']) && $options['zerofill']) {
            $spec    .= ' %s';
            $params[] = 'ZEROFILL';
            $types[]  = self::TYPE_LITERAL;
        }

        if (isset($options['unsigned']) && $options['unsigned']) {
            $spec    .= ' %s';
            $params[] = 'UNSIGNED';
            $types[]  = self::TYPE_LITERAL;
        }

        $types[]  = self::TYPE_LITERAL;
        $params[] = (!$this->isNullable) ? 'NOT NULL' : '';

        $types[]  = ($this->default !== null) ? self::TYPE_VALUE : self::TYPE_LITERAL;
        $params[] = ($this->default !== null) ? $this->default : '';

        return array(array(
            $spec,
            $params,
            $types,
        ));
    }
}
