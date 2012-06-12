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
 * @package    Zend_Gdata
 * @subpackage Analytics
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\GData;

/**
 * @category   Zend
 * @package    Zend_Gdata
 * @subpackage Books
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Analytics extends Gdata
{

	const AUTH_SERVICE_NAME = 'analytics';
	const ANALYTICS_FEED_URI = 'https://www.google.com/analytics/feeds';
	const ANALYTICS_ACCOUNT_FEED_URI = 'https://www.google.com/analytics/feeds/accounts';

	public static $namespaces = array(
        array('ga', 'http://schemas.google.com/analytics/2009', 1, 0)
    );

    /**
     * Create Zend_Gdata_Analytics object
     *
     * @param Zend_Http_Client $client (optional) The HTTP client to use when
     *          when communicating with the Google Apps servers.
     * @param string $applicationId The identity of the app in the form of Company-AppName-Version
     */
	public function __construct($client = null, $applicationId = 'MyCompany-MyApp-1.0')
    {
        $this->registerPackage('Zend\Gdata\Analytics');
        $this->registerPackage('Zend\Gdata\Analytics\Extension');
        parent::__construct($client, $applicationId);
        $this->_httpClient->setParameterPost(array('service' => self::AUTH_SERVICE_NAME));
    }

    /**
     * Retrieve account feed object
     *
     * @return Zend_Gdata_Analytics_AccountFeed
     */
    public function getAccountFeed()
    {
        $uri = self::ANALYTICS_ACCOUNT_FEED_URI . '/default?prettyprint=true';
        return parent::getFeed($uri, 'Zend\Gdata\Analytics\AccountFeed');
    }

    /**
     * Retrieve data feed object
     * 
     * @param mixed $location
     * @return Zend_Gdata_Analytics_DataFeed
     */
    public function getDataFeed($location)
    {
		if ($location == null) {
            $uri = self::ANALYTICS_FEED_URI;
        } elseif ($location instanceof Query) {
            $uri = $location->getQueryUrl();
        } else {
            $uri = $location;
        }
        return parent::getFeed($uri, 'Zend\Gdata\Analytics\DataFeed');
    }

    /**
     * Returns a new DataQuery object.
     * 
     * @return Zend_Gdata_Analytics_DataQuery
     */
    public function newDataQuery()
    {
    	return new \Zend\Gdata\Analytics\DataQuery();
    }
}
