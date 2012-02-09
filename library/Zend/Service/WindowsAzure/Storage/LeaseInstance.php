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
 * @subpackage Storage
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

namespace Zend\Service\WindowsAzure\Storage;

 Zend\Service\WindowsAzure\Storage\StorageEntityAbstract;
 
/**
 * @category   Zend
 * @package    Zend\Service\WindowsAzure
 * @subpackage Storage
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * 
 * @property string  $Container       Container name
 * @property string  $Name            Name
 * @property string  $LeaseId         Lease id
 * @property string  $LeaseTime       Time remaining in the lease period, in seconds. This header is returned only for a successful request to break the lease. It provides an approximation as to when the lease period will expire.
 */
class LeaseInstance extends StorageEntityAbstract
{
    /**
     * Constructor
     * 
     * @param string  $containerName   Container name
     * @param string  $name            Name
     * @param string  $leaseId         Lease id
     * @param string  $leaseTime       Time remaining in the lease period, in seconds. This header is returned only for a successful request to break the lease. It provides an approximation as to when the lease period will expire.
     */
    public function __construct($containerName, $name, $leaseId, $leaseTime) 
    {	        
        $this->_data = array(
            'container'        => $containerName,
            'name'             => $name,
            'leaseid'          => $leaseId,
            'leasetime'        => $leaseTime
        );
    }
}
