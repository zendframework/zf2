<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_CodeGenerator
 * @subpackage PHP
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\CodeGenerator\Php;

class PhpValue extends AbstractPhp
{
    /**#@+
     * Constant values
     */
    const TYPE_AUTO     = 'auto';
    const TYPE_BOOLEAN  = 'boolean';
    const TYPE_BOOL     = 'bool';
    const TYPE_NUMBER   = 'number';
    const TYPE_INTEGER  = 'integer';
    const TYPE_INT      = 'int';
    const TYPE_FLOAT    = 'float';
    const TYPE_DOUBLE   = 'double';
    const TYPE_STRING   = 'string';
    const TYPE_ARRAY    = 'array';
    const TYPE_CONSTANT = 'constant';
    const TYPE_NULL     = 'null';
    const TYPE_OBJECT   = 'object';
    const TYPE_OTHER    = 'other';
    /**#@-*/

    const OUTPUT_MULTIPLE_LINE = 'multipleLine';
    const OUTPUT_SINGLE_LINE = 'singleLine';

    /**
     * @var array of reflected constants
     */
    protected static $_constants = array();

    /**
     * @var mixed
     */
    protected $_value = null;

    /**
     * @var string
     */
    protected $_type  = self::TYPE_AUTO;

    /**
     * @var int
     */
    protected $_arrayDepth = 1;

    /**
     * @var string
     */
    protected $_outputMode = self::OUTPUT_MULTIPLE_LINE;

    /**
     * @var array
     */
    protected $_allowedTypes = null;

    /**
     * _init()
     *
     * This method will prepare the constant array for this class
     */
    protected function _init()
    {
        if(count(self::$_constants) == 0) {
            $reflect = new \ReflectionClass(get_class($this));
            foreach ($reflect->getConstants() as $name => $value) {
                if (substr($name, 0, 4) == 'TYPE') {
                    self::$_constants[$name] = $value;
                }
            }
            unset($reflect);
        }
    }

    /**
     * isValidConstantType()
     *
     * @return bool
     */
    public function isValidConstantType()
    {
        if ($this->_type == self::TYPE_AUTO) {
            $type = $this->_getAutoDeterminedType($this->_value);
        } else {
            $type = $this->_type;
        }

        // valid types for constants
        $scalarTypes = array(
            self::TYPE_BOOLEAN,
            self::TYPE_BOOL,
            self::TYPE_NUMBER,
            self::TYPE_INTEGER,
            self::TYPE_INT,
            self::TYPE_FLOAT,
            self::TYPE_DOUBLE,
            self::TYPE_STRING,
            self::TYPE_CONSTANT,
            self::TYPE_NULL
            );

        return in_array($type, $scalarTypes);
    }

    /**
     * setValue()
     *
     * @param mixed $value
     * @return \Zend\CodeGenerator\Php\PhpPropertyValue
     */
    public function setValue($value)
    {
        $this->_value = $value;
        return $this;
    }

    /**
     * getValue()
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->_value;
    }

    /**
     * setType()
     *
     * @param string $type
     * @return \Zend\CodeGenerator\Php\PhpPropertyValue
     */
    public function setType($type)
    {
        $this->_type = $type;
        return $this;
    }

    /**
     * getType()
     *
     * @return string
     */
    public function getType()
    {
        return $this->_type;
    }

    /**
     * setArrayDepth()
     *
     * @param int $arrayDepth
     * @return \Zend\CodeGenerator\Php\PhpPropertyValue
     */
    public function setArrayDepth($arrayDepth)
    {
        $this->_arrayDepth = $arrayDepth;
        return $this;
    }

    /**
     * getArrayDepth()
     *
     * @return int
     */
    public function getArrayDepth()
    {
        return $this->_arrayDepth;
    }

    /**
     * _getValidatedType()
     *
     * @param string $type
     * @return string
     */
    protected function _getValidatedType($type)
    {
        if (($constName = array_search($type, self::$_constants)) !== false) {
            return $type;
        }

        return self::TYPE_AUTO;
    }

    /**
     * _getAutoDeterminedType()
     *
     * @param mixed $value
     * @return string
     */
    public function _getAutoDeterminedType($value)
    {
        switch (gettype($value)) {
            case 'boolean':
                return self::TYPE_BOOLEAN;
            case 'integer':
                return self::TYPE_INT;
            case 'string':
                return self::TYPE_STRING;
            case 'double':
            case 'float':
            case 'integer':
                return self::TYPE_NUMBER;
            case 'array':
                return self::TYPE_ARRAY;
            case 'NULL':
                return self::TYPE_NULL;
            case 'object':
            case 'resource':
            case 'unknown type':
            default:
                return self::TYPE_OTHER;
        }
    }

    /**
     * generate()
     *
     * @return string
     */
    public function generate()
    {
        $type = $this->_type;

        if ($type != self::TYPE_AUTO) {
            $type = $this->_getValidatedType($type);
        }

        $value = $this->_value;

        if ($type == self::TYPE_AUTO) {
            $type = $this->_getAutoDeterminedType($value);

            if ($type == self::TYPE_ARRAY) {
                $rii = new \RecursiveIteratorIterator(
                    $it = new \RecursiveArrayIterator($value),
                    \RecursiveIteratorIterator::SELF_FIRST
                    );
                foreach ($rii as $curKey => $curValue) {
                    if (!$curValue instanceof self) {
                        $curValue = new self(array('value' => $curValue));
                        $rii->getSubIterator()->offsetSet($curKey, $curValue);
                    }
                    $curValue->setArrayDepth($rii->getDepth());
                }
                $value = $rii->getSubIterator()->getArrayCopy();
            }

        }

        $output = '';

        switch ($type) {
            case self::TYPE_BOOLEAN:
            case self::TYPE_BOOL:
                $output .= ( $value ? 'true' : 'false' );
                break;
            case self::TYPE_STRING:
                $output .= self::escape($value);
                break;
            case self::TYPE_NULL:
                $output .= 'null';
                break;
            case self::TYPE_NUMBER:
            case self::TYPE_INTEGER:
            case self::TYPE_INT:
            case self::TYPE_FLOAT:
            case self::TYPE_DOUBLE:
            case self::TYPE_CONSTANT:
                $output .= $value;
                break;
            case self::TYPE_ARRAY:
                $output .= 'array(';
                $curArrayMultiblock = false;
                if (count($value) > 1) {
                    $curArrayMultiblock = true;
                    if ($this->_outputMode == self::OUTPUT_MULTIPLE_LINE) {
                        $output .= self::LINE_FEED . str_repeat($this->_indentation, $this->_arrayDepth + 1);
                    }
                }
                $outputParts = array();
                $noKeyIndex = 0;
                foreach ($value as $n => $v) {
                    $v->setArrayDepth($this->_arrayDepth + 1);
                    $partV = $v->generate();
                    if ($n === $noKeyIndex) {
                        $outputParts[] = $partV;
                        $noKeyIndex++;
                    } else {
                        $outputParts[] = (is_int($n) ? $n : self::escape($n)) . ' => ' . $partV;
                    }
                }
                $padding = ($this->_outputMode == self::OUTPUT_MULTIPLE_LINE)
                    ? self::LINE_FEED . str_repeat($this->_indentation, $this->_arrayDepth + 1)
                    : ' ';
                $output .= implode(',' . $padding, $outputParts);
                if ($curArrayMultiblock == true && $this->_outputMode == self::OUTPUT_MULTIPLE_LINE) {
                    $output .= self::LINE_FEED . str_repeat($this->_indentation, $this->_arrayDepth + 1);
                }
                $output .= ')';
                break;
            case self::TYPE_OTHER:
            default:
                throw new Exception\RuntimeException(
                    "Type '".get_class($value)."' is unknown or cannot be used as property default value."
                );
        }

        return $output;
    }

    /**
     * Quotes value for PHP code.
     *
     * @param string $input Raw string.
     * @param bool $quote Whether add surrounding quotes or not.
     * @return string PHP-ready code.
     */
    public static function escape($input, $quote = true)
    {
        $output = addcslashes($input, "'");

        // adds quoting strings
        if ($quote) {
            $output = "'" . $output . "'";
        }

        return $output;
    }
}
