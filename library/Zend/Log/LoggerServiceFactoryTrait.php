<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Log;

use Zend\Log\Logger;

/**
 * Logger service factory trait.
 *
 * Share functionality across {@link LoggerServiceFactory} and {@link LoggerAbstractServiceFactory}.
 */
trait LoggerServiceFactoryTrait
{
    /**
     * @var \Zend\Log\Logger
     */
    private $defaultLogger;

    /**
     * @return \Zend\Log\Logger
     */
    public function getDefaultLogger()
    {
        if (null === $this->defaultLogger) {
            $this->setDefaultLogger($this->createLogger());
        }
        return $this->defaultLogger;
    }

    /**
     * @param Logger $defaultLogger
     */
    public function setDefaultLogger(Logger $defaultLogger)
    {
        $this->defaultLogger = $defaultLogger;
    }

    /**
     * @param array $config
     * @return \Zend\Log\Logger
     */
    private function createLogger($config = array())
    {
        $logger = new Logger($config);
        return $logger;
    }
}
