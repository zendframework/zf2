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
 * @package    Zend_GData_Analytics
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\GData\Analytics;

use Zend\GData\Analytics;

/**
 * @category   Zend
 * @package    Zend_GData_Analytics
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend\GData
 * @group      Zend\GData\Analytics
 */
class DataFeedTest extends \PHPUnit_Framework_TestCase
{
    public $testData = array(
        'blogger.com' => 68140,
        'google.com'  => 29666,
        'stumbleupon.com' => 4012, 
        'google.co.uk' => 2968, 
        'google.co.in' => 2793,        
    );
    
    public function setUp()
    {
        $this->dataFeed = new Analytics\DataFeed(
            file_get_contents(dirname(__FILE__) . '/_files/TestDataFeed.xml'),
            true
        );
    }

    public function testDataFeed()
    {
        $count = count($this->testData);
        $this->assertEquals($this->dataFeed->count(), $count);
        foreach ($this->dataFeed->entries as $entry) {
            $this->assertTrue($entry instanceof Analytics\DataEntry);
        }
    }
    
    public function testGetters()
    {
        $sources = array_keys($this->testData);
        $values = array_values($this->testData);
        
        foreach ($this->dataFeed as $index => $row) {
            $source = $row->getDimension(Analytics\DataQuery::DIMENSION_SOURCE);
            $medium = $row->getDimension('ga:medium');
            $visits = $row->getMetric('ga:visits');
            $visitsValue = $row->getValue('ga:visits');
            
            $this->assertEquals("$medium", 'referral');
            $this->assertEquals("$source", $sources[$index]);
            $this->assertEquals("$visits", $values[$index]);
            $this->assertEquals("$visitsValue", $values[$index]);
        }
    }
}
