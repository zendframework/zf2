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
 * @property string $Url             The address of the storage account.
 * @property string $ServiceName     The name of the storage account.
 * @property string $Description	 A description of the storage account.
 * @property string $AffinityGroup   The affinity group with which this storage account is associated.
 * @property string $Location        The geo-location of the storage account in Windows Azure, if your storage account is not associated with an affinity group.
 * @property string $Label           The label for the storage account.
 */
class StorageServiceInstance extends ServiceEntityAbstract
{    
    /**
     * Constructor
     * 
     * @param string $url             The address of the storage account.
     * @param string $serviceName     The name of the storage account.
	 * @param string $description	  A description of the storage account.
	 * @param string $affinityGroup   The affinity group with which this storage account is associated.
	 * @param string $location        The geo-location of the storage account in Windows Azure, if your storage account is not associated with an affinity group.
	 * @param string $label           The label for the storage account.
	 */
    public function __construct($url, $serviceName, $description = '', $affinityGroup = '', $location = '', $label = '') 
    {	        
        $this->_data = array(
            'url'              => $url,
            'servicename'      => $serviceName,
            'description'      => $description,
            'affinitygroup'    => $affinityGroup,
            'location'         => $location,
            'label'            => base64_decode($label)        
        );
    }
}
