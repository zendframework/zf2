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
 * @property string $Id              The request ID of the asynchronous request.
 * @property string $Status          The status of the asynchronous request. Possible values include InProgress, Succeeded, or Failed.
 * @property string $ErrorCode	     The management service error code returned if the asynchronous request failed. 
 * @property string $ErrorMessage    The management service error message returned if the asynchronous request failed. 
 */
class OperationStatusInstance extends ServiceEntityAbstract
{    
    /**
     * Constructor
     * 
     * @param string $id              The request ID of the asynchronous request.
     * @param string $status          The status of the asynchronous request. Possible values include InProgress, Succeeded, or Failed.
     * @param string $errorCode	      The management service error code returned if the asynchronous request failed. 
     * @param string $errorMessage    The management service error message returned if the asynchronous request failed.
	 */
    public function __construct($id, $status, $errorCode, $errorMessage) 
    {	        
        $this->_data = array(
            'id'              => $id,
            'status'          => $status,
            'errorcode'       => $errorCode,
            'errormessage'    => $errorMessage     
        );
    }
}
