<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Validator
 */

namespace Zend\Validator;

use Traversable;
use Zend\Stdlib\ArrayUtils;

/**
 * @category   Zend
 * @package    Zend_Validate
 */
class InArray extends AbstractValidator
{
    const NOT_IN_ARRAY = 'notInArray';

    /**
     * @var array
     */
    protected $messageTemplates = array(
        self::NOT_IN_ARRAY => "The input was not found in the haystack",
    );

    /**
     * Haystack of possible values
     *
     * @var array
     */
    protected $haystack;

    /**
     * Whether a strict in_array() invocation is used
     *
     * @var boolean
     */
    protected $strict = false;

    /**
     * Whether a recursive search should be done
     *
     * @var boolean
     */
    protected $recursive = false;

    /**
     * Sets validator options
     *
     * @param  array|Traversable $options
     * @throws Exception\InvalidArgumentException
     */
    public function __construct($options = null)
    {
        if ($options instanceof Traversable) {
            $options = ArrayUtils::iteratorToArray($options);
        }
        if (!is_array($options)) {
            throw new Exception\InvalidArgumentException('Array expected as parameter');
        } else {
            $count = func_num_args();
            $temp  = array();
            if ($count > 1) {
                $temp['haystack'] = func_get_arg(0);
                $temp['strict']   = func_get_arg(1);
                $options = $temp;
            } else {
                $temp = func_get_arg(0);
                if (!array_key_exists('haystack', $options)) {
                    $options = array();
                    $options['haystack'] = $temp;
                } else {
                    $options = $temp;
                }
            }
        }

        $this->setHaystack($options['haystack']);
        if (array_key_exists('strict', $options)) {
            $this->setStrict($options['strict']);
        }

        if (array_key_exists('recursive', $options)) {
            $this->setRecursive($options['recursive']);
        }

        parent::__construct();
    }

    /**
     * Returns the haystack option
     *
     * @return mixed
     */
    public function getHaystack()
    {
        return $this->haystack;
    }

    /**
     * Sets the haystack option
     *
     * @param  mixed $haystack
     * @return InArray Provides a fluent interface
     */
    public function setHaystack(array $haystack)
    {
        $this->haystack = $haystack;
        return $this;
    }

    /**
     * Returns the strict option
     *
     * @return boolean
     */
    public function getStrict()
    {
        return $this->strict;
    }

    /**
     * Sets the strict option
     *
     * @param  boolean $strict
     * @return InArray Provides a fluent interface
     */
    public function setStrict($strict)
    {
        $this->strict = (boolean) $strict;
        return $this;
    }

    /**
     * Returns the recursive option
     *
     * @return boolean
     */
    public function getRecursive()
    {
        return $this->recursive;
    }

    /**
     * Sets the recursive option
     *
     * @param  boolean $recursive
     * @return InArray Provides a fluent interface
     */
    public function setRecursive($recursive)
    {
        $this->recursive = (boolean) $recursive;
        return $this;
    }

    /**
     * Returns true if and only if $value is contained in the haystack option. If the strict
     * option is true, then the type of $value is also checked.
     *
     * @param  mixed $value
     * @return boolean
     */
    public function isValid($value)
    {
        $this->setValue($value);
        if ($this->getRecursive()) {
            $iterator = new \RecursiveIteratorIterator(new \RecursiveArrayIterator($this->haystack));
            foreach($iterator as $element) {
                if ($this->strict) {
                    if ($element === $value) {
                        return true;
                    }
                } elseif ($element == $value) {
                    return true;
                }
            }
        } else {
            if (in_array($value, $this->haystack, $this->strict)) {
                return true;
            }
        }

        $this->error(self::NOT_IN_ARRAY);
        return false;
    }
}
