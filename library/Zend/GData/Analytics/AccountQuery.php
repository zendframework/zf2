<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_GData
 */

namespace Zend\GData\Analytics;

use Zend\GData;

/**
 * @category   Zend
 * @package    Zend_Gdata
 * @subpackage Analytics
 */
class AccountQuery extends GData\Query
{
    const ANALYTICS_FEED_URI = 'https://www.googleapis.com/analytics/v2.4/management/accounts';

    /**
     * The default URI used for feeds.
     */
    protected $_defaultFeedUri = self::ANALYTICS_FEED_URI;
    
    /**
     * @var string
     */
    protected $_accountId = '~all';
    /**
     * @var string
     */
    protected $_webpropertyId = '~all';
    /**
     * @var string
     */
    protected $_profileId = '~all';
   
    /**
     * @var bool
     */ 
    protected $_webproperties = false;
    /**
     * @var bool
     */
    protected $_profiles = false;
    /**
     * @var bool
     */
    protected $_goals = false;
    
    /**
     * @param string $accountId
     * @return AccountQuery
     */
    public function setAccountId($accountId)
    {
        $this->_accountId = $accountId;
        return $this;
    }
    
    /**
     * @return string
     */
    public function getAccountId()
    {
        return $this->_accountId;
    }

    /**
     * @param string $webpropertyId
     * @return AccountQuery
     */
    public function setWebpropertyId($webpropertyId)
    {
        $this->_webpropertyId = $webpropertyId;
        return $this;
    }
    
    /**
     * @return string
     */
    public function getWebpropertyId()
    {
        return $this->_webpropertyId;
    }
    
    /**
     * @param string $profileId
     * @return AccountQuery
     */
    public function setProfileId($profileId)
    {
        $this->_profileId = $profileId;
        return $this;
    }

    /**
     * @return string
     */
    public function getProfileId()
    {
        return $this->_profileId;
    }
    
    /**
     * @param string $accountId
     * @return AccountQuery
     */
    public function webproperties($accountId = '~all')
    {
        $this->_webproperties = true;
        $this->setAccountId($accountId);    
        return $this;
    }
    
    /**
     * @param string $webpropertyId
     * @param string $accountId
     * @return AccountQuery
     */
    public function profiles($webpropertyId = '~all', $accountId = '~all')
    {
        $this->_profiles = true;
        if (null !== $accountId) {
            $this->setAccountId($accountId);    
        }
        $this->setWebpropertyId($webpropertyId);
        return $this;
    }
    
    /**
     * @param string $webpropertyId
     * @param string $accountId
     * @param string $accountId
     * @return AccountQuery
     */
    public function goals($profileId = '~all', $webpropertyId = '~all', $accountId = '~all')
    {
        $this->_goals = true;
        if (null !== $accountId) {
            $this->setAccountId($accountId);    
        }
        if (null !== $webpropertyId) {
            $this->setWebpropertyId($webpropertyId);    
        }
        $this->setProfileId($profileId);
        return $this;
    }
    
    /**
     * @return string url
     */
    public function getQueryUrl()
    {
        $url = $this->_defaultFeedUri;
        
        // add account id
        if ($this->_webproperties or $this->_profiles or $this->_goals) {
            $url .= '/' . $this->_accountId . '/webproperties';
        }
        
        if ($this->_profiles or $this->_goals) {
            $url .= '/' . $this->_webpropertyId . '/profiles';
        }
        
        if ($this->_goals) {
            $url .= '/' . $this->_profileId . '/goals';
        }

        $url .= $this->getQueryString();
        return $url;
    }
}