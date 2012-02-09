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
 * @package    Zend\Service\WindowsAzure
 * @subpackage RetryPolicy
 * @version    $Id$
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Service\WindowsAzure\RetryPolicy;

use Zend\Service\WindowsAzure\RetryPolicy\NoRetry as NoRetry,
    Zend\Service\WindowsAzure\RetryPolicy\RetryN as RetryN;

/**
 * @category   Zend
 * @package    Zend\Service\WindowsAzure
 * @subpackage RetryPolicy
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class RetryPolicyAbstract
{
    /**
     * Execute function under retry policy
     * 
     * @param string|array $function       Function to execute
     * @param array        $parameters     Parameters for function call
     * @return mixed
     */
    public abstract function execute($function, $parameters = array());
    
    /**
     * Create a Zend\Service\WindowsAzure\RetryPolicy\NoRetry instance
     * 
     * @return Zend\Service\WindowsAzure\RetryPolicy\NoRetry
     */
    public static function noRetry()
    {
        return new NoRetry();
    }
    
    /**
     * Create a Zend\Service\WindowsAzure\RetryPolicy\RetryN instance
     * 
     * @param int $count                    Number of retries
     * @param int $intervalBetweenRetries   Interval between retries (in milliseconds)
     * @return Zend\Service\WindowsAzure\RetryPolicy\RetryN
     */
    public static function retryN($count = 1, $intervalBetweenRetries = 0)
    {
        return new RetryN($count, $intervalBetweenRetries);
    }
}