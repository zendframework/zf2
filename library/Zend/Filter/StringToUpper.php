<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Filter;

use Traversable;

class StringToUpper extends AbstractUnicode
{
    /**
     * @var array
     */
    protected $options = array(
        'encoding' => null,
    );

    /**
     * Constructor
     *
     * @param string|array|Traversable $encodingOrOptions OPTIONAL
     */
    public function __construct($encodingOrOptions = null)
    {
        if ($encodingOrOptions !== null) {
            if (!static::isOptions($encodingOrOptions)) {
                $this->setEncoding($encodingOrOptions);
            } else {
                $this->setOptions($encodingOrOptions);
            }
        }
    }

    /**
     * Defined by Zend\Filter\FilterInterface
     *
     * Returns the string $value, converting characters to lowercase as necessary
     *
     * @param  string $value
     * @return string
     */
    public function filter($value)
    {
        if(!is_scalar($value)){
            throw new Exception\InvalidArgumentException(sprintf(
                '%s expects parameter to be scalar, "%s" given',
                __METHOD__,
                (is_object($value) ? get_class($value) : gettype($value))
            ));
        }

        if ($this->options['encoding'] !== null) {
            return mb_strtoupper((string) $value,  $this->options['encoding']);
        }

        return strtoupper((string) $value);
    }
}
