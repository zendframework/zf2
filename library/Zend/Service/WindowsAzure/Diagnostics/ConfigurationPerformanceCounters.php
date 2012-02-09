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
 * @subpackage Diagnostics
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

namespace Zend\Service\WindowsAzure\Diagnostics;

use Zend\Service\WindowsAzure\Diagnostics\ConfigurationObjectBaseAbstract;

/**
 * @category   Zend
 * @package    Zend\Service\WindowsAzure
 * @subpackage Diagnostics
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 *
 * @property	int		BufferQuotaInMB						Buffer quota in MB
 * @property	int		ScheduledTransferPeriodInMinutes	Scheduled transfer period in minutes
 * @property	array	Subscriptions						Subscriptions
 */
class ConfigurationPerformanceCounters
	extends ConfigurationObjectBaseAbstract
{
    /**
     * Constructor
     * 
	 * @param	int		$bufferQuotaInMB					Buffer quota in MB
	 * @param	int		$scheduledTransferPeriodInMinutes	Scheduled transfer period in minutes
	 */
    public function __construct($bufferQuotaInMB = 0, $scheduledTransferPeriodInMinutes = 0) 
    {	        
        $this->_data = array(
            'bufferquotainmb'        		=> $bufferQuotaInMB,
            'scheduledtransferperiodinminutes' 	=> $scheduledTransferPeriodInMinutes,
            'subscriptions'			=> array()
        );
    }
    
	/**
	 * Add subscription
	 * 
 	 * @param	string	$counterSpecifier					Counter specifier
 	 * @param	int		$sampleRateInSeconds				Sample rate in seconds
	 */
    public function addSubscription($counterSpecifier, $sampleRateInSeconds = 1)
    {
    	$this->_data['subscriptions'][$counterSpecifier] = new Zend\Service\WindowsAzure\Diagnostics\PerformanceCounterSubscription($counterSpecifier, $sampleRateInSeconds);
    }
    
	/**
	 * Remove subscription
	 * 
 	 * @param	string	$counterSpecifier					Counter specifier
	 */
    public function removeSubscription($counterSpecifier)
    {
    	if (isset($this->_data['subscriptions'][$counterSpecifier])) {
    		unset($this->_data['subscriptions'][$counterSpecifier]);
    	}
    }
}