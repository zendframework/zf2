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
 * @package    Zend_Filter
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Filter;

use Traversable;
use Zend\Stdlib\ArrayUtils;

/**
 * @category   Zend
 * @package    Zend_Filter
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class StringTrim extends AbstractFilter
{
    /**
     * @var array
     */
    protected $options = array(
        'char_list' => null,
    );

    /**
     * Sets filter options
     *
     * @param  string|array|Traversable $options
     */
    public function __construct($charlistOrOptions = null)
    {
        if ($charlistOrOptions !== null) {
            if (!is_array($charlistOrOptions)
                && !$charlistOrOptions  instanceof Traversable)
            {
                $this->setCharList($charlistOrOptions);
            } else {
                $this->setOptions($charlistOrOptions);
            }
        }
    }

    /**
     * Sets the charList option
     *
     * @param  string $charList
     * @return StringTrim Provides a fluent interface
     */
    public function setCharList($charList)
    {
        if (empty($charList)) {
            $charList = null;
        }
        $this->options['char_list'] = $charList;
        return $this;
    }

    /**
     * Returns the charList option
     *
     * @return string|null
     */
    public function getCharList()
    {
        return $this->options['char_list'];
    }

    /**
     * Defined by Zend\Filter\FilterInterface
     *
     * Returns the string $value with characters stripped from the beginning and end
     *
     * @param  string $value
     * @return string
     */
    public function filter($value)
    {
        // Do not filter non-string values
        if (!is_string($value)) {
            return $value;
        }

        if (null === $this->options['char_list']) {
            return $this->unicodeTrim((string) $value);
        }

        return $this->unicodeTrim((string) $value, $this->options['char_list']);
    }

    /**
     * Unicode aware trim method
     * Fixes a PHP problem
     *
     * @param string $value
     * @param string $charlist
     * @return string
     */
    protected function unicodeTrim($value, $charlist = '\\\\s')
    {
        $chars = preg_replace(
            array('/[\^\-\]\\\]/S', '/\\\{4}/S', '/\//'),
            array('\\\\\\0', '\\', '\/'),
            $charlist
        );

        $pattern = '/^[' . $chars . ']*|[' . $chars . ']*$/sSD';

        return preg_replace($pattern, '', $value);
    }
}
