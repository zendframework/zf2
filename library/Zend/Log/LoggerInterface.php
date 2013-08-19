<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Log;

use Psr\Log\LoggerInterface as PsrLoggerInterface;
use Traversable;

interface LoggerInterface extends PsrLoggerInterface
{
    /**
     * @param string $message
     * @param array|Traversable $context
     * @return LoggerInterface
     */
    public function emerg($message, array $context = array());

    /**
     * @param string $message
     * @param array|Traversable $context
     * @return LoggerInterface
     */
    public function alert($message, array $context = array());

    /**
     * @param string $message
     * @param array|Traversable $context
     * @return LoggerInterface
     */
    public function crit($message, array $context = array());

    /**
     * @param string $message
     * @param array|Traversable $context
     * @return LoggerInterface
     */
    public function err($message, array $context = array());

    /**
     * @param string $message
     * @param array|Traversable $context
     * @return LoggerInterface
     */
    public function warn($message, array $context = array());

    /**
     * @param string $message
     * @param array|Traversable $context
     * @return LoggerInterface
     */
    public function notice($message, array $context = array());

    /**
     * @param string $message
     * @param array|Traversable $context
     * @return LoggerInterface
     */
    public function info($message, array $context = array());

    /**
     * @param string $message
     * @param array|Traversable $context
     * @return LoggerInterface
     */
    public function debug($message, array $context = array());
}
