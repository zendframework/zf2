<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Cloud
 */

namespace Zend\Cloud\Infrastructure\Adapter;

/**
 * Adapter interface for infrastructure service
 * 
 * @package    Zend_Cloud
 * @subpackage Infrastructure
 */
interface AdapterInterface
{ 
    const HTTP_ADAPTER = 'http_adapter'; 

    /**
     * The max. amount of time, in seconds, to wait for a status change
     */
    const TIMEOUT_STATUS_CHANGE = 30;

    /**
     * The time step, in seconds, for the status change
     */
    const TIME_STEP_STATUS_CHANGE = 5;

    /**
     * Return a list of the available instances
     *
     * @return \Zend\Cloud\Infrastructure\InstanceList
     */ 
    public function listInstances(); 
 
    /**
     * Return the status of an instance
     *
     * @param  string $id
     * @return string
     */ 
    public function statusInstance($id); 

    /**
     * Wait for status $status with a timeout of $timeout seconds
     * 
     * @param  string $id
     * @param  string $status
     * @param  integer $timeout 
     * @return boolean
     */
    public function waitStatusInstance($id, $status, $timeout = self::TIMEOUT_STATUS_CHANGE);
    
    /**
     * Return the public DNS name of the instance
     * 
     * @param  string $id
     * @return string|boolean 
     */
    public function publicDnsInstance($id);
    
    /**
     * Reboot an instance
     *
     * @param string $id
     * @return boolean
     */ 
    public function rebootInstance($id); 
 
    /**
     * Create a new instance
     *
     * @param  string $name
     * @param  array $options
     * @return boolean
     */ 
    public function createInstance($name, $options); 
 
    /**
     * Stop the execution of an instance
     *
     * @param  string $id
     * @return boolean
     */ 
    public function stopInstance($id); 
 
    /**
     * Start the execution of an instance
     *
     * @param  string $id
     * @return boolean
     */ 
    public function startInstance($id); 
 
    /**
     * Destroy an instance
     *
     * @param  string $id
     * @return boolean
     */ 
    public function destroyInstance($id); 
 
    /**
     * Return all the available instances images
     *
     * @return \Zend\Cloud\Infrastructure\ImageList
     */ 
    public function imagesInstance(); 
    
    /**
     * Return all the available zones
     * 
     * @return array
     */
    public function zonesInstance();
    
    /**
     * Return the system informations about the $metric of an instance
     *
     * @param  string $id
     * @param  string $metric
     * @param  array $options
     * @return array
     */ 
    public function monitorInstance($id, $metric, $options = null); 
 
    /**
     * Run arbitrary shell script on an instance
     *
     * @param  string $id
     * @param  array $param
     * @param  string|array $cmd
     * @return string|array
     */ 
    public function deployInstance($id, $param, $cmd);
            
    /**
     * Get the adapter instance
     * 
     * @return object
     */
    public function getAdapter();
    
    /**
     * Get the adapter result
     * 
     * @return array
     */
    public function getAdapterResult();
    
    /**
     * Get the last HTTP response
     * 
     * @return \Zend\Http\Response
     */
    public function getLastHttpResponse();
    
    /**
     * Get the last HTTP request
     * 
     * @return string
     */
    public function getLastHttpRequest();
    
    /**
     * Return true if the last request was successful
     * 
     * @return boolean 
     */
    public function isSuccessful();
    
    /**
     * Get the error message
     * 
     * @return string 
     */
    public function getErrorMsg();
    
    /**
     * Get the error code
     * 
     * @return string
     */
    public function getErrorCode();
} 
