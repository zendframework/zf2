<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Log
 */

namespace Zend\Log;

use Zend\Log\LoggerInterface;

/**
 * Logger aware interface
 *
 * @package    Zend_Log
 */
interface LoggerAwareInterface
{
    public function setLogger(LoggerInterface $logger);
}
