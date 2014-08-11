<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Db\Sql\Ddl\Column;

class Integer extends Column
{
    /**
     * @var int
     */
    protected $length;

    /**
     * @param null|string     $name
     * @param bool            $nullable
     * @param null|string|int $default
     * @param array           $options
     */
    public function __construct($name, $nullable = false, $default = null, array $options = array())
    {
        $this->setName($name);
        $this->setNullable($nullable);
        $this->setDefault($default);
        $this->setOptions($options);
    }

    /**
     * Reload to add numeric options (zerofill, unsigned)
     * @return array
     */
    public function getExpressionData()
    {
        $spec = $this->specification;
        $options = $this->getOptions();

        $params   = array();
        $params[] = $this->name;
        $params[] = $this->type;

        $types = array(self::TYPE_IDENTIFIER, self::TYPE_LITERAL);

        // length

        if (isset($options['length']) && $options['length']) {
            $spec    .= '(%s)';
            $params[] = $options['length'];
            $types[]  = self::TYPE_LITERAL;
        }

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

        if (!$this->isNullable) {
            $spec    .= ' %s';
            $params[] = 'NOT NULL';
            $types[]  = self::TYPE_LITERAL;
        }

        if ($this->default !== null) {
            $spec    .= ' DEFAULT %s';
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
