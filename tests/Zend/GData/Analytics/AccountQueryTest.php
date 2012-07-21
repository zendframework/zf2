<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_GData
 */

namespace ZendTest\GData\Analytics;

use Zend\GData\Analytics\AccountQuery;

/**
 * @category   Zend
 * @package    Zend_GData_Analytics
 * @subpackage UnitTests
 * @group      Zend_GData
 * @group      Zend_GData_Analytics
 */
class AccountQueryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AccountQuery
     */
    public $accountQuery;

    public function setUp()
    {
        $this->accountQuery = new AccountQuery();
        $this->queryBase = AccountQuery::ANALYTICS_FEED_URI;
    }

    public function testWebpropertiesAll()
    {
        $this->accountQuery->webproperties();
        $allQuery = $this->accountQuery->getQueryUrl();
        
        $this->assertEquals(
            $this->queryBase . '/~all/webproperties', 
            $allQuery
        );
    }
    
    public function testWebpropertiesSpecific()
    {
        $this->accountQuery->webproperties(12345678);
        $specificQuery = $this->accountQuery->getQueryUrl();
        
        $this->assertEquals(
            $this->queryBase . '/12345678/webproperties', 
            $specificQuery
        );
    }
    
    public function testProfilesAll()
    {
        $this->accountQuery->profiles();
        $allQuery = $this->accountQuery->getQueryUrl();
        
        $this->assertEquals(
            $this->queryBase . '/~all/webproperties/~all/profiles', 
            $allQuery
        );
    }
    
    public function testProfilesSpecific()
    {
        $this->accountQuery->profiles('U-87654321-0', 87654321);
        $specificQuery = $this->accountQuery->getQueryUrl();
        
        $this->assertEquals(
            $this->queryBase . '/87654321/webproperties/U-87654321-0/profiles', 
            $specificQuery
        );
    }
    
    public function testGoalsAll()
    {
        $this->accountQuery->goals();
        $allQuery = $this->accountQuery->getQueryUrl();
        
        $this->assertEquals(
            $this->queryBase . '/~all/webproperties/~all/profiles/~all/goals', 
            $allQuery
        );
    }
    
    public function testGoalsSpecific()
    {
        $this->accountQuery->goals(42, 'U-87654321-0', 87654321);
        $specificQuery = $this->accountQuery->getQueryUrl();
        
        $this->assertEquals(
            $this->queryBase . '/87654321/webproperties/U-87654321-0/profiles/42/goals', 
            $specificQuery
        );
    }
    
    public function testChainedProperties()
    {
        $this->accountQuery
            ->goals(42)
            ->profiles('U-87654321-0')
            ->webproperties(87654321);
        $specificQuery = $this->accountQuery->getQueryUrl();
        
        $this->assertEquals(
            $this->queryBase . '/87654321/webproperties/U-87654321-0/profiles/42/goals', 
            $specificQuery
        );
    }
}
