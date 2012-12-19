<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_InputFilter
 */

namespace Zend\InputFilter;

/**
 * @category   Zend
 * @package    Zend_InputFilter
 */
interface ConfigurableInputInterface
{
    /**
     * Set options for an input
     *
     * @param  array $options
     * @return mixed
     */
    public function setOptions($options);

    /**
     * Get options for an input
     *
     * @return array
     */
    public function getOptions();
}
