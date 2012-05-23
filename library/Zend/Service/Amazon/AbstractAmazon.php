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
     * Method to get the pesudo-ISO8601 request date
     * 
     *          "2012-05-15T20:58:54.000Z"
     *          
     * Unless setRequestDate was set (as when testing the service)
     * 
     * @return string
     */
    public function getRequestIsoDate()
    {
        if(!empty($this->_requestDate)) {
            $date = $this->_requestDate;
        } else {
            $date = null;
        }
        
        if(!is_object($date)) {
            
            $date = new Date();
            
        } elseif(!empty($date->preserve)) {
            
            $this->_requestDate = null;
            
        }
        
        //DateTimeZone UTC
        
        return $date->get('Y-m-d\TH:i:s.000\Z'); //DATE_ISO8601 is not compatible with S3
        
        
    }    
}
