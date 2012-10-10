<?php
/**
 * Zend Framework (http://framework.zend.com/)
*
* @link      http://github.com/zendframework/zf2 for the canonical source repository
* @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
* @license   http://framework.zend.com/license/new-bsd New BSD License
* @package   Zend_Log
*/

namespace Zend\Log\Processor;

use Zend\Console\Console;

/**
 * @category   Zend
 * @package    Zend_Log
 * @subpackage Processor
 */
class RequestId implements ProcessorInterface
{

    /**
     * Adds a identfier for the request to the log.
     * This enables to filter the log for messages belonging to a specific request
     *
     * @param array $event event data
     * @return array event data
     */
    public function process(array $event)
    {
        if(!isset($event['extra'])) {
            $event['extra'] = array();
        }

        $event['extra']['requestId'] = $this->getIdentifier();
        return $event;
    }

    /**
     * Provide unique identifier for a request
     *
     * @return  array:
     */
    protected function getIdentifier()
    {
        $requestTime = (version_compare(PHP_VERSION, '5.4.0') >= 0) ? $_SERVER['REQUEST_TIME_FLOAT'] : $_SERVER['REQUEST_TIME'];
        $remoteAddr = Console::isConsole() ? 'local' : $_SERVER['REMOTE_ADDR'];

        return md5($requestTime . $remoteAddr);
    }
}
