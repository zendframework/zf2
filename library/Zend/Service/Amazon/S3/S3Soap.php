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
 * @subpackage Amazon_S3
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Service\Amazon\S3;

use Zend\Crypt;
use Zend\Service\Amazon;
use Zend\Service\Amazon\S3\Exception;
use Zend\Soap\Client as SoapClient; //Not to mix with Rest Client

/**
 * Amazon S3 SOAP PHP connection class
 *
 * @category   Zend
 * @package    Zend_Service
 * @subpackage Amazon_S3
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @see        http://docs.amazonwebservices.com/AmazonS3/2006-03-01/
 */
class S3 extends \Zend\Service\Amazon\AbstractAmazon
{

    /**
     * SOAP client
     *
     * @var Zend\Soap\Client
     */
    protected $_soapClient = null;

    /**
     * SOAP Fault Code
     *
     * @var string|null
     */
    protected $_soapFaultCode;

    /**
     * SOAP Fault Message
     *
     * @var string|null
     */
    protected $_soapFaultMessage;

    /**
     * SOAP Fault Details
     *
     * @var object|null
     */
    protected $_soapFaultDetails;
        
    const S3_WSDL = 'http://doc.s3.amazonaws.com/2006-03-01/AmazonS3.wsdl';

    const S3_ACL_PRIVATE = 'private';
    const S3_ACL_PUBLIC_READ = 'public-read';
    const S3_ACL_PUBLIC_WRITE = 'public-read-write';
    const S3_ACL_AUTH_READ = 'authenticated-read';

    const S3_REQUESTPAY_HEADER = 'x-amz-request-payer';
    const S3_ACL_HEADER = 'x-amz-acl';
    const S3_CONTENT_TYPE_HEADER = 'Content-Type';

    /**
     * Set SOAP Client
     *
     * @param \Zend\Soap\Client $client
     * @return S3
     */
    public function setSoapClient(SoapClient $client)
    {
        $this->_soapClient = $client;
        return $this;
    }
        
    /**
     * Get SOAP Client
     *
     * @return \Zend\Soap\Client
     */
    public function getSoapClient()
    {
        
        if (is_null($this->_soapClient)) {
            $this->_soapClient = new SoapClient(self::S3_WSDL);
        }
        
        return $this->_soapClient;
    }

    /**
     * Get response header of last SOAP call
     *
     * @return string
     */
    public function getLastResponseHeader()
    {
        return $this->getSoapClient()->__getLastResponseHeaders();
    }
    
    
    
    /**
     * Constructor
     *
     * @param string $accessKey
     * @param string $secretKey
     * @param string $region
     */
    public function __construct($accessKey=null, $secretKey=null, $region=null)
    {
        parent::__construct($accessKey, $secretKey, $region);

    }

    /**
     * Verify if the bucket name is valid
     * For reference: http://docs.amazonwebservices.com/AmazonS3/latest/dev/BucketRestrictions.html
     *
     * @param string $bucket
     * @return boolean
     */
    public function _validBucketName($bucket, $location = null)
    {
        if (empty($location))
            $location = ''; //US Standard region code is an empty string 
        
        if ($location == '') {
            //US Standard Region
            $maxLen = 255;
            
            //Capital letters, dashes and underscores allowed
            $pregExpression = '/[^a-zA-Z0-9\._-]/';
            
            //Label can be an IP address
            $isIpAddress = false;
            
            //Label can be empty or starting with dashes
            $labelError = false;
            
        } else {
            //More restricted rules apply
            $maxLen = 63;
            
            //No Capital letters or underscores
            $pregExpression = '/[^a-z0-9\.-]/';
            
            //Cannot be an IP address
            $isIpAddress = preg_match('/(\d){1,3}\.(\d){1,3}\.(\d){1,3}\.(\d){1,3}/', $bucket);
            
            //Labels must not be empty and should start and end with letter or number
            $labelError = false; 
            $labels     = explode('.', $bucket);           
            foreach ($labels as $label) {
                if (empty($label)) {
                    $labelError = true; 
                    break;
                }
                
                if ($label[0] == '-' || $label[strlen($label)-1] == '-') {
                    $labelError = true;
                    break;
                }
            }
        }
        
        
        $len = strlen($bucket);
        if ($len < 3 || $len > $maxLen) {
            throw new Exception\InvalidArgumentException("Bucket name \"$bucket\" must be between 3 and 255 characters long");
        }

        if (preg_match($pregExpression, $bucket)) {
            throw new Exception\InvalidArgumentException("Bucket name \"$bucket\" contains invalid characters");
        }

        if ($isIpAddress) {
            throw new Exception\InvalidArgumentException("Bucket name \"$bucket\" cannot be an IP address");
        }
        
        if ($labelError) {
            throw new Exception\InvalidArgumentException("Bucket name \"$bucket\" labels must start and end with a letter or a number");
        }
        return true;
    }

    /**
     * Add a new bucket
     *  
     * @param  string $bucket
     * @return boolean
     */
    public function createBucket($bucket, $location = null)
    {
        $this->_validBucketName($bucket, $location);
                
        $response = $this->_soapRequest('CreateBucket', array('Bucket' => $bucket));

               
        if ($response === false && !empty($this->_soapFaultCode)) 
            return false;
        else
            return true;

        
        /* Common $this->_soapFaultCode:
        * // Bucket not available
        * // "faultstring" => "The requested bucket name is not available. The bucket namespace is shared by all users of the system. Please select a different name and try again."
        * // "faultcode"   => "ns1:Client.BucketAlreadyExists"
        * // "detail"      => object(stdClass)#4 (1) { "BucketName" => "bucket"  }
        */
        
        /*
         *  Structure of response (for debugging):
         *  
            string(265) "HTTP/1.1 200 OK
            x-amz-id-2: qaR9SV0w+5CXr4fzm/gTMVhNA1QFNkhh25Wm6qIRHELBhDzHAZV1DYlPWR6EP5g+
            x-amz-request-id: 260C92BACA86BA52
            Content-Type: application/soap+xml; charset=utf-8
            Transfer-Encoding: chunked
            Date: Tue, 15 May 2012 23:32:15 GMT
            Server: AmazonS3
            "
            object(stdClass)#4 (0) {
            }
        */
    }

    /**
     * List the S3 buckets
     *
     * @return array|false
     */
    public function getBuckets()
    {
    
        $response = $this->_soapRequest('ListAllMyBuckets');
         
        if (is_null($response) && !empty($this->_soapFaultCode)) {
            return false;
        }
         
        //Structure of response
         
        /* object(stdClass)#6 (1) { ["ListAllMyBucketsResponse"]=>
         object(stdClass)#7 (2) {
            ["Owner"]=>
                object(stdClass)#8 (2) {
                    ["ID"]=> "983059eac8a35394299acb23b0db43cbb05a014"
                    ["DisplayName"]=> "John"
                }
            ["Buckets"]=>
                object(stdClass)#9 (1) {
                    ["Bucket"]=>
                        array(12) {
                            object(stdClass)#10 (2) {
                                ["Name"]=> "Bucket1"
                                ["CreationDate"]=> string(24) "2012-05-15T21:48:58.000Z"
                            }
        */
         
        $bucketsTemp = array();
        if (!empty($response->ListAllMyBucketsResponse->Buckets->Bucket)) {
            $bucketsTemp = $response->ListAllMyBucketsResponse->Buckets->Bucket;
        }
    
        $bucketList = array();
        foreach ($bucketsTemp as $bucketObj) {
            $bucketList[] = $bucketObj->Name;
        }
         
        return $bucketList;
    }
    
    
    /**
     * Remove a given bucket. All objects in the bucket must be removed prior
     * to removing the bucket.
     *
     * @param  string $bucket
     * @return boolean
     */
    public function removeBucket($bucket, $location)
    {
        $this->_validBucketName($bucket, $location);
                
        $response = $this->_soapRequest('DeleteBucket', array('Bucket' => $bucket));

        if ($response === false && !empty($this->_soapFaultCode))
            return false;
        else
            return true;
        
        //What returns the DeleteBucket?
        /*
         * DeleteBucket response (for debugging):
         * 
          
            <DeleteBucketResponse xmlns="http://s3.amazonaws.com/doc/2006-03-01">
            <DeleteBucketResponse>
            <Code>204</Code>
            <Description>No Content</Description>
            </DeleteBucketResponse>
            </DeleteBucketResponse>
        
        */
    }

    
    /**
     * SOAP request
     *
     * @return array|false
     * @TODO  match soap fault code with appropriate exception
     */
    protected function _soapRequest($call, $options = array())
    {

        $this->_soapFaultCode    = null;
        $this->_soapFaultMessage = null;
        $this->_soapFaultDetail  = null;
        $soapResponse            = null; 
        
        $isoDate    = $this->getRequestIsoDate();
        $signature  = $this->_calculateSignature($call, $isoDate);
                
        if (!is_array($options)) {
            $options = array();
        }
        
        $options['AWSAccessKeyId'] = $this->_getAccessKey();
        $options['Timestamp']      = $isoDate;
        $options['Signature']      = $signature;
        
        
        try {            
            $soapClient   = $this->getSoapClient();
            $soapResponse = $soapClient->$call($options);
        } catch (\SoapFault $fault) {
            $this->_soapFaultCode    = $fault->faultcode;
            $this->_soapFaultMessage = $fault->faultstring; //We should use standard message string
            $this->_soapFaultDetail  = (isset($fault->detail))? : null;

            if (defined('S3SOAP_DEBUG')) {
                var_dump($options);
                var_dump($fault);
            }
            
            switch($this->_soapFaultCode) {
                case 'ns1:Client.InvalidAccessKeyId':
                    
                    /*
                     *  ["faultstring"]=>
                    string(65) "The AWS Access Key Id you provided does not exist in our records."
                    ["faultcode"]=>
                    string(29) "ns1:Client.InvalidAccessKeyId"
                    ["detail"]=>
                    object(stdClass)#4 (1) {
                    ["AWSAccessKeyId"]=>
                    string(21) "AKIAJVKKT2SWTIFPTJFQp"
                    }
                    
                    */
                    
                    break;
                case 'ns1:Client.SignatureDoesNotMatch':
                    // "faultstring" => "The request signature we calculated does not match the signature you provided. Check your key and signing method."
                    // "detail" => stdClass
                    //     "StringToSignBytes" => "41 6d 61 7a 6f 6e 53 ..."
                    //     "SignatureProvided" => "qxhG6d7zNiq/6q1bkpufydj2JAE="
                    //     "StringToSign"      => "AmazonS3ListAllMyBuckets2012-05-15T22:13:25.000Z"
                    //     "AWSAccessKeyId"    => "ABCDEFGHIJKLMNOPQRSTUVWXYZ"

                    break;
                    
                case 'ns1:Client.RequestTimeTooSkewed':
                    //The difference between the request time and the current time is too large
                    
                    
                    break;
                case 'Sender':
                    // Not a valid method                    
                    // "faultstring" => "Function ("ListAllMyBucketsp") is not a valid method for this service"
                    // "faultcodens" => "http://www.w3.org/2003/05/soap-envelope"

                    
                    // Param is missing
                      // "faultstring" => "SOAP-ERROR: Encoding: object has no 'Bucket' property"
                    // "faultcodens" => "http://www.w3.org/2003/05/soap-envelope"
                    break;
                default:
            }
            
        }

        return $soapResponse;        
    }
    
    
    /**
     * Generates SOAP signature
     * 
     * @return string
     */
    protected function _calculateSignature($operation, $isoDate)
    {
        $secretKey    = $this->_getSecretKey();
        $stringToSign = utf8_encode( "AmazonS3" . $operation . $isoDate );
        
        $signature = base64_encode(\Zend\Crypt\Hmac::compute($secretKey, 'sha1', $stringToSign, \Zend\Crypt\Hmac::BINARY));
        return $signature; 
    
    }
    
    /**
     * Remove all objects in the bucket.
     *
     * @param string $bucket
     * @return boolean
     */
    public function cleanBucket($bucket)
    {
        $objects = $this->getObjectsByBucket($bucket);
        if (!$objects) {
            return false;
        }

        foreach ($objects as $object) {
            $this->removeObject("$bucket/$object");
        }
        return true;
    }

    /**
     * List the objects in a bucket.
     *
     * Provides the list of object keys that are contained in the bucket.  Valid params include the following.
     * prefix - Limits the response to keys which begin with the indicated prefix. You can use prefixes to separate a bucket into different sets of keys in a way similar to how a file system uses folders.
     * marker - Indicates where in the bucket to begin listing. The list will only include keys that occur lexicographically after marker. This is convenient for pagination: To get the next page of results use the last key of the current page as the marker.
     * max-keys - The maximum number of keys you'd like to see in the response body. The server might return fewer than this many keys, but will not return more.
     * delimiter - Causes keys that contain the same string between the prefix and the first occurrence of the delimiter to be rolled up into a single result element in the CommonPrefixes collection. These rolled-up keys are not returned elsewhere in the response.
     *
     * @param  string $bucket
     * @param array $params S3 GET Bucket Paramater
     * @return array|false
     * @TODO implement method
     */
    public function getObjectsByBucket($bucket, $params = array())
    {
    }
    

    

    
    /**
     * Put file to S3 as object
     *
     * @param string $path   File name
     * @param string $object Object name
     * @param array  $meta   Metadata
     * @return boolean
     * @TODO implement method S3Soap::putFile()
     */
    public function putFile($path, $object, $meta=null)
    {
    }


    /**
     * Remove a given object
     *
     * @param  string $object
     * @return boolean
     * @TODO implement method S3Soap::removeObject()
     */
    public function removeObject($object)
    {

    }


    /**
     * Move an object
     *
     * Performs a copy to dest + verify + remove source
     *
     * @param string $sourceObject  Source object name
     * @param string $destObject    Destination object name
     * @param array  $meta          (OPTIONAL) Metadata to apply to destination object.
     *                              Set to null to retain existing metadata.
     * @TODO implement method S3Soap::moveObject
     */
    public function moveObject($sourceObject, $destObject, $meta = null)
    {
    }
    

    /**
     * Get metadata information for a given object
     *
     * @param  string $object
     * @return array|false
     * @TODO   implement method S3Soap::getInfo()
     */
    public function getInfo($object)
    {
    }
    
    /**
     * Get an object
     *
     * @param  string $object
     * @param  bool   $paidobject This is "requestor pays" object
     * @return string|false
     * @TODO  implement method S3Soap::getObject()
     */
    public function getObject($object, $paidobject=false)
    {
    }
    
    /**
     * Get an object using streaming
     *
     * Can use either provided filename for storage or create a temp file if none provided.
     *
     * @param  string $object Object path
     * @param  string $streamfile File to write the stream to
     * @param  bool   $paidobject This is "requestor pays" object
     * @return StreamResponse|false
     * @TODO implement method S3Soap::getObjectStream()
     */
    public function getObjectStream($object, $streamfile = null, $paidobject=false)
    {
    }
    
    /**
     * Upload an object by a PHP string
     *
     * @param  string $object Object name
     * @param  string|resource $data   Object data (can be string or stream)
     * @param  array  $meta   Metadata
     * @return boolean
     * @TODO   implement method S3Soap::putObject()
     */
    public function putObject($object, $data, $meta=null)
    {
    }
    
    

    /**
     * Checks if a given bucket name is available
     *
     * @param  string $bucket
     * @return boolean
     * @TODO   implement method S3Soap::isBucketAvailable()
     */
    public function isBucketAvailable($bucket)
    {
    
    }
    
    /**
     * Checks if a given object exists
     *
     * @param  string $object
     * @return boolean
     * @TODO   implement method S3Soap::isObjectAvailable()
     */
    public function isObjectAvailable($object)
    {
    
    }
    
    /**
     * Copy an object
     *
     * @param  string $sourceObject  Source object name
     * @param  string $destObject    Destination object name
     * @param  array  $meta          (OPTIONAL) Metadata to apply to desination object.
     *                               Set to null to copy metadata from source object.
     * @return boolean
     * @TODO   implement method S3Soap::copyObject
     */
    public function copyObject($sourceObject, $destObject, $meta = null)
    {
    }
    
}
