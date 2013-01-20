<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Log
 */

namespace Zend\Log;

use Traversable;

/**
 * @category   Zend
 * @package    Zend_Log
 */
class LoggerComposite implements LoggerInterface
{
    /**
     * @var LoggerInterface[]
     */
    protected $loggers = array();

    /**
     * Add logger
     *
     * @param LoggerInterface $logger
     * @return LoggerComposite
     */
    public function addLogger(LoggerInterface $logger)
    {
        $this->loggers[] = $logger;
        return $this;
    }

    /**
     * Check for logger
     *
     * @param LoggerInterface $logger
     * @return bool
     */
    public function hasLogger(LoggerInterface $logger)
    {
        return in_array($logger, $this->loggers, true);
    }

    /**
     * Remove logger
     *
     * @param LoggerInterface $logger
     * @return bool
     */
    public function removeLogger(LoggerInterface $logger)
    {
        $key = array_search($logger, $this->loggers, true);
        if (false === $key) {
            return false;
        }
        unset($this->loggers[$key]);
        return true;
    }

    /**
     * @param string $message
     * @param array|Traversable $extra
     * @return LoggerInterface
     */
    public function emerg($message, $extra = array())
    {
        foreach ($this->loggers as $logger) {
            $logger->emerg($message, $extra);
        }
    }

    /**
     * @param string $message
     * @param array|Traversable $extra
     * @return LoggerInterface
     */
    public function alert($message, $extra = array())
    {
        foreach ($this->loggers as $logger) {
            $logger->alert($message, $extra);
        }
    }

    /**
     * @param string $message
     * @param array|Traversable $extra
     * @return LoggerInterface
     */
    public function crit($message, $extra = array())
    {
        foreach ($this->loggers as $logger) {
            $logger->crit($message, $extra);
        }
    }

    /**
     * @param string $message
     * @param array|Traversable $extra
     * @return LoggerInterface
     */
    public function err($message, $extra = array())
    {
        foreach ($this->loggers as $logger) {
            $logger->err($message, $extra);
        }
    }

    /**
     * @param string $message
     * @param array|Traversable $extra
     * @return LoggerInterface
     */
    public function warn($message, $extra = array())
    {
        foreach ($this->loggers as $logger) {
            $logger->warn($message, $extra);
        }
    }

    /**
     * @param string $message
     * @param array|Traversable $extra
     * @return LoggerInterface
     */
    public function notice($message, $extra = array())
    {
        foreach ($this->loggers as $logger) {
            $logger->notice($message, $extra);
        }
    }

    /**
     * @param string $message
     * @param array|Traversable $extra
     * @return LoggerInterface
     */
    public function info($message, $extra = array())
    {
        foreach ($this->loggers as $logger) {
            $logger->info($message, $extra);
        }
    }

    /**
     * @param string $message
     * @param array|Traversable $extra
     * @return LoggerInterface
     */
    public function debug($message, $extra = array())
    {
        foreach ($this->loggers as $logger) {
            $logger->debug($message, $extra);
        }
    }
}
