<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\InputFilter;

/**
 * Input interface
 */
interface InputInterface
{
    /**
     * Set the name of the input filter
     *
     * @param  string $name
     * @return void
     */
    public function setName($name);

    /**
     * Get the name of the input filter
     *
     * @return string
     */
    public function getName();

    /**
     * Check if the input filter is valid
     *
     * @return bool
     */
    public function isValid();
}
