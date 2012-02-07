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
 * @subpackage Management
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

namespace Zend\Service\WindowsAzure\Management;

use Zend\Service\WindowsAzure\Management\ServiceEntityAbstract;

/**
 * @category   Zend
 * @package    Zend\Service\WindowsAzure
 * @subpackage Management
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * 
 * @property string $operationId The globally unique identifier (GUID) of the operation.
 * @property string $operationObjectId The target object for the operation. 
 * @property string $operationName The name of the performed operation.
 * @property array  $operationParameters The collection of parameters for the performed operation.
 * @property array  $operationCaller A collection of attributes that identifies the source of the operation.
 * @property array  $operationStatus The current status of the operation.
 */
class SubscriptionOperationInstance extends ServiceEntityAbstract
{    
    /**
     * Constructor
     * 
     * @param string $operationId The globally unique identifier (GUID) of the operation.
     * @param string $operationObjectId The target object for the operation. 
     * @param string $operationName The name of the performed operation.
     * @param array  $operationParameters The collection of parameters for the performed operation.
     * @param array  $operationCaller A collection of attributes that identifies the source of the operation.
     * @param array  $operationStatus The current status of the operation.
     * @param string $operationStartedTime The time that the operation started to execute.
     * @param string $operationCompletedTime The time that the operation finished executing.
     */
    public function __construct($operationId, $operationObjectId, $operationName, $operationParameters = array(), $operationCaller = array(), $operationStatus = array(), $operationStartedTime = '', $operationCompletedTime = '') 
    {	        
        $this->_data = array(
            'operationid'            => $operationId,
	        'operationobjectid'      => $operationObjectId,
	        'operationname'          => $operationName,
	        'operationparameters'    => $operationParameters,
	        'operationcaller'        => $operationCaller,
	        'operationstatus'        => $operationStatus,
	        'operationstartedtime'   => $operationStartedTime,
	        'operationcompletedtime' => $operationCompletedTime
        );
    }
    
	/**
	 * Add operation parameter
	 * 
 	 * @param	string	$name	Name
 	 * @param	string	$value  Value
	 */
    public function addOperationParameter($name, $value)
    {
    	$this->_data['operationparameters'][$name] = $value;
    }
}
