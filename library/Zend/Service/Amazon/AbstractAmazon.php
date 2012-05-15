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
 * @package    Zend_Service
 * @subpackage Amazon
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Service\Amazon;

/**
 * Abstract Amazon class that handles the credentials for any of the Web Services that
 * Amazon offers
 *
 * @category   Zend
 * @package    Zend_Service
 * @subpackage Amazon
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class AbstractAmazon extends \Zend\Service\AbstractService
{
    /**
     * @var string Amazon Access Key
     */
    protected static $_defaultAccessKey = null;

    /**
     * @var string Amazon Secret Key
     */
    protected static $_defaultSecretKey = null;

    /**
     * @var string Amazon Secret Key
     */
    protected $_secretKey;

    /**
     * @var string Amazon Access Key
     */
    protected $_accessKey;

    /**
     * Request date - useful for testing services with signature
     * 
     * @var int|string|null Request date - useful for testing services with signature
     */
    protected $_requestDate = null;
    
    /**
     * @var \Zend\Http\Response Response of last request
     */
    protected $_lastResponse = null;
    

    /**
     * Set the keys to use when accessing SQS.
     *
     * @param  string $access_key       Set the default Access Key
     * @param  string $secret_key       Set the default Secret Key
     * @return void
     */
    public static function setKeys($accessKey, $secretKey)
    {
        self::$_defaultAccessKey = $accessKey;
        self::$_defaultSecretKey = $secretKey;
    }
    
    /**
     * Set the RFC1123 request date - useful for testing the services with signature
     *
     * @param null|int|string $date
     * @return void
     */
    public function setRequestDate($date)
    {
        $this->_requestDate = $date;
    }
    

    /**
     * Create Amazon client.
     *
     * @param  string $access_key       Override the default Access Key
     * @param  string $secret_key       Override the default Secret Key
     * @return void
     */
    public function __construct($accessKey=null, $secretKey=null)
    {
        if(!$accessKey) {
            $accessKey = self::$_defaultAccessKey;
        }
        if(!$secretKey) {
            $secretKey = self::$_defaultSecretKey;
        }

        if(!$accessKey || !$secretKey) {
            throw new Exception\InvalidArgumentException("AWS keys were not supplied");
        }
        $this->_accessKey = $accessKey;
        $this->_secretKey = $secretKey;
    }



    /**
     * Method to fetch the Access Key
     *
     * @return string
     */
    protected function _getAccessKey()
    {
        return $this->_accessKey;
    }

    /**
     * Method to fetch the Secret AWS Key
     *
     * @return string
     */
    protected function _getSecretKey()
    {
        return $this->_secretKey;
    }
    
    /**
     * Method to get the Response object of the last call to the service,
     * null if no call was done or no response was obtained
     *
     * @return \Zend\Http\Response
     */
    public function getLastResponse()
    {
        return $this->_lastResponse;
    }
    

    /**
     * Method to get the request date - returns gmdate(DATE_RFC1123, time())
     * 
     *     "Tue, 15 May 2012 15:18:31 +0000"
     *     
     * Unless setRequestDate was set (as when testing the service)
     *
     * @return string
     */
    public function getRequestDate()
    {
        if(is_null($this->_requestDate))
            $this->_requestDate = time();
        
        if(is_numeric($this->_requestDate))
            $this->_requestDate = gmdate(DATE_RFC1123, $this->_requestDate); 
        
        return $this->_requestDate;
    }
}
