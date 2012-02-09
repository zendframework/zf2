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

class Manager
{
	/**
	 * Blob storage client
	 * 
	 * @var Zend\Service\WindowsAzure\Storage\Blob
	 */
	protected $_blobStorageClient = null;
	
	/**
	 * Control container name
	 * 
	 * @var string
	 */
	protected $_controlContainer = '';

	/**
	 * Create a new instance of Zend\Service\WindowsAzure\Diagnostics\Manager
	 * 
	 * @param Zend\Service\WindowsAzure\Storage\Blob $blobStorageClient Blob storage client
	 * @param string $controlContainer Control container name
	 */
	public function __construct(Zend\Service\WindowsAzure\Storage\Blob $blobStorageClient = null, $controlContainer = 'wad-control-container')
	{
		$this->_blobStorageClient = $blobStorageClient;
		$this->_controlContainer = $controlContainer;

		$this->_ensureStorageInitialized();
	}

	/**
	 * Ensure storage has been initialized
	 */
	protected function _ensureStorageInitialized()
	{
		if (!$this->_blobStorageClient->containerExists($this->_controlContainer)) {
			$this->_blobStorageClient->createContainer($this->_controlContainer);
		}
	}
	
	/**
	 * Get default configuration values
	 * 
	 * @return Zend\Service\WindowsAzure\Diagnostics\ConfigurationInstance
	 */
	public function getDefaultConfiguration()
	{
		return new Zend\Service\WindowsAzure\Diagnostics\ConfigurationInstance();
	}
	
	/**
	 * Checks if a configuration for a specific role instance exists.
	 * 
	 * @param string $roleInstance Role instance name, can be found in $_SERVER['RdRoleId'] when hosted on Windows Azure.
	 * @return boolean
	 * @throws Zend\Service\WindowsAzure\Diagnostics\Exception
	 */
	public function configurationForRoleInstanceExists($roleInstance = null)
	{
		if (is_null($roleInstance)) {
                    throw new Exception\InvalidArgumentException('Role instance should be specified. Try reading $_SERVER[\'RdRoleId\'] for this information if the application is hosted on Windows Azure Fabric or Development Fabric.');
		}

		return $this->_blobStorageClient->blobExists($this->_controlContainer, $roleInstance);
	}
	
	/**
	 * Checks if a configuration for current role instance exists. Only works on Development Fabric or Windows Azure Fabric.
	 * 
	 * @return boolean
	 * @throws Zend\Service\WindowsAzure\Diagnostics\Exception
	 */
	public function configurationForCurrentRoleInstanceExists()
	{
		if (!isset($_SERVER['RdRoleId'])) {
                    throw new Exception\InvalidArgumentException('Server variable \'RdRoleId\' is unknown. Please verify the application is running in Development Fabric or Windows Azure Fabric.');
		}

		return $this->_blobStorageClient->blobExists($this->_controlContainer, $this->_getCurrentRoleInstanceId());
	}
	
	/**
	 * Get configuration for current role instance. Only works on Development Fabric or Windows Azure Fabric.
	 * 
	 * @return Zend\Service\WindowsAzure\Diagnostics\ConfigurationInstance
	 * @throws Zend\Service\WindowsAzure\Diagnostics\Exception
	 */
	public function getConfigurationForCurrentRoleInstance()
	{
		if (!isset($_SERVER['RdRoleId'])) {
                    throw new Exception\InvalidArgumentException('Server variable \'RdRoleId\' is unknown. Please verify the application is running in Development Fabric or Windows Azure Fabric.');
		}
		return $this->getConfigurationForRoleInstance($this->_getCurrentRoleInstanceId());
	}
	
	/**
	 * Get the current role instance ID. Only works on Development Fabric or Windows Azure Fabric.
	 * 
	 * @return string
	 * @throws Zend\Service\WindowsAzure\Diagnostics\Exception
	 */
	protected function _getCurrentRoleInstanceId()
	{
		if (!isset($_SERVER['RdRoleId'])) {
                    throw new Exception\InvalidArgumentException('Server variable \'RdRoleId\' is unknown. Please verify the application is running in Development Fabric or Windows Azure Fabric.');
		}
		
		if (strpos($_SERVER['RdRoleId'], 'deployment(') === false) {
			return $_SERVER['RdRoleId'];
		} else {
			$roleIdParts = explode('.', $_SERVER['RdRoleId']);
			return $roleIdParts[0] . '/' . $roleIdParts[2] . '/' . $_SERVER['RdRoleId'];
		}

		if (!isset($_SERVER['RoleDeploymentID']) && !isset($_SERVER['RoleInstanceID']) && !isset($_SERVER['RoleName'])) {
			throw new Exception\RunTimeException('Server variables \'RoleDeploymentID\', \'RoleInstanceID\' and \'RoleName\' are unknown. Please verify the application is running in Development Fabric or Windows Azure Fabric.');
		}
		
		if (strpos($_SERVER['RdRoleId'], 'deployment(') === false) {
			return $_SERVER['RdRoleId'];
		} else {
			return $_SERVER['RoleDeploymentID'] . '/' . $_SERVER['RoleInstanceID'] . '/' . $_SERVER['RoleName'];
		}
	}
	
	/**
	 * Set configuration for current role instance. Only works on Development Fabric or Windows Azure Fabric.
	 * 
	 * @param Zend\Service\WindowsAzure\Diagnostics\ConfigurationInstance $configuration Configuration to apply
	 * @throws Zend\Service\WindowsAzure\Diagnostics\Exception
	 */
	public function setConfigurationForCurrentRoleInstance(Zend\Service\WindowsAzure\Diagnostics\ConfigurationInstance $configuration)
	{
		if (!isset($_SERVER['RdRoleId'])) {
			throw new Exception\InvalidArgumentException('Server variable \'RdRoleId\' is unknown. Please verify the application is running in Development Fabric or Windows Azure Fabric.');
		}
		
		$this->setConfigurationForRoleInstance($this->_getCurrentRoleInstanceId(), $configuration);
	}
	
	/**
	 * Get configuration for a specific role instance
	 * 
	 * @param string $roleInstance Role instance name, can be found in $_SERVER['RdRoleId'] when hosted on Windows Azure.
	 * @return Zend\Service\WindowsAzure\Diagnostics\ConfigurationInstance
	 * @throws Zend\Service\WindowsAzure\Diagnostics\Exception
	 */
	public function getConfigurationForRoleInstance($roleInstance = null)
	{
		if (is_null($roleInstance)) {
			throw new Exception\InvalidArgumentException('Role instance should be specified. Try reading $_SERVER[\'RdRoleId\'] for this information if the application is hosted on Windows Azure Fabric or Development Fabric.');
		}

		if ($this->_blobStorageClient->blobExists($this->_controlContainer, $roleInstance)) {
			$configurationInstance = new Zend\Service\WindowsAzure\Diagnostics\ConfigurationInstance();
			$configurationInstance->loadXml( $this->_blobStorageClient->getBlobData($this->_controlContainer, $roleInstance) );
			return $configurationInstance;
		}

		return new Zend\Service\WindowsAzure\Diagnostics\ConfigurationInstance();
	}
	
	/**
	 * Set configuration for a specific role instance
	 * 
	 * @param string $roleInstance Role instance name, can be found in $_SERVER['RdRoleId'] when hosted on Windows Azure.
	 * @param Zend\Service\WindowsAzure\Diagnostics\ConfigurationInstance $configuration Configuration to apply
	 * @throws Zend\Service\WindowsAzure\Diagnostics\Exception
	 */
	public function setConfigurationForRoleInstance($roleInstance = null, Zend\Service\WindowsAzure\Diagnostics\ConfigurationInstance $configuration)
	{
		if (is_null($roleInstance)) {
			throw new Exception\InvalidArgumentException('Role instance should be specified. Try reading $_SERVER[\'RdRoleId\'] for this information if the application is hosted on Windows Azure Fabric or Development Fabric.');
		}

		$this->_blobStorageClient->putBlobData($this->_controlContainer, $roleInstance, $configuration->toXml(), array(), null, array('Content-Type' => 'text/xml'));
	}
}