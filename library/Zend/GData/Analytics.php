<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_GData
 */

namespace Zend\GData;

use Zend\Http\Client;

/**
 * @category   Zend
 * @package    Zend_Gdata
 * @subpackage Analytics
 */
class Analytics extends GData
{
    const AUTH_SERVICE_NAME = 'analytics';
    const ANALYTICS_FEED_URI = 'https://www.googleapis.com/analytics/v2.4/data'; 
    const ANALYTICS_ACCOUNT_FEED_URI = 'https://www.googleapis.com/analytics/v2.4/management/accounts';

    public static $namespaces = array(
        array('analytics', 'http://schemas.google.com/analytics/2009', 1, 0),
        array('ga', 'http://schemas.google.com/ga/2009', 1, 0)
    );

    /**
     * Create Gdata object
     *
     * @param \Zend\Http\Client $client
     * @param string $applicationId The identity of the app in the form of
     *          Company-AppName-Version
     */
    public function __construct(Client $client = null, $applicationId = 'MyCompany-MyApp-1.0')
    {
        $this->registerPackage('Zend\Gdata\Analytics');
        $this->registerPackage('Zend\Gdata\Analytics\Extension');
        parent::__construct($client, $applicationId);
        $this->_httpClient->setParameterPost(array('service' => self::AUTH_SERVICE_NAME));
    }

    /**
     * Retrieve account feed object
     *
     * @param string|\Zend\Uri\Uri $uri
     * @return Analytics\AccountFeed
     */
    public function getAccountFeed($uri = self::ANALYTICS_ACCOUNT_FEED_URI)
    {
        if ($uri instanceof Query) {
            $uri = $uri->getQueryUrl();
        }
        return parent::getFeed($uri, 'Zend\GData\Analytics\AccountFeed');
    }
    
    /**
     * Retrieve data feed object
     *
     * @param string|\Zend\Uri\Uri $uri
     * @return Analytics\DataFeed
     */
    public function getDataFeed($uri = self::ANALYTICS_FEED_URI)
    {
        if ($uri instanceof Query) {
            $uri = $uri->getQueryUrl();
        }
        return parent::getFeed($uri, 'Zend\GData\Analytics\DataFeed');
    }

    /**
     * Returns a new DataQuery object.
     *
     * @return Analytics\DataQuery
     */
    public function newDataQuery()
    {
        return new Analytics\DataQuery();
    }

    /**
     * Returns a new AccountQuery object.
     *
     * @return Analytics\AccountQuery
     */
    public function newAccountQuery()
    {
        return new Analytics\AccountQuery();
    }
}
