<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Log\Writer\FirePhp;

interface FirePhpInterface
{
    /**
     * Determine whether or not FirePHP is enabled
     *
     * @return bool
     */
    public function getEnabled();

    /**
     * Log an error message
     *
     * @param string $line
     * @param string $label
     * @param array $options 
     */
    public function error($line, $label = null, $options = array());

    /**
     * Log a warning
     *
     * @param string $line
     * @param string $label
     * @param array $options 
     */
    public function warn($line, $label = null, $options = array());

    /**
     * Log informational message
     *
     * @param string $line
     * @param string $label
     * @param array $options 
     */
    public function info($line, $label = null, $options = array());

    /**
     * Log a trace
     *
     * @param string $line
     * @param string $label
     * @param array $options 
     */
    public function trace($line, $label = null, $options = array());

    /**
     * Log a message
     *
     * @param string $line
     * @param string $label
     * @param array $options 
     */
    public function log($line, $label = null, $options = array());
}
