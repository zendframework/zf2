<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_GData
 */

namespace ZendTest\GData;
use Zend\GData\Extension;

/**
 * @category   Zend
 * @package    Zend_GData_OpenSearch
 * @subpackage UnitTests
 * @group      Zend_GData
 * @group      Zend_GData_OpenSearch
 */
class OpenSearchItemsPerPageTest extends \PHPUnit_Framework_TestCase
{

    public function setUp() {
        $this->openSearchItemsPerPageText = file_get_contents(
                'Zend/GData/_files/OpenSearchItemsPerPageElementSample1.xml',
                true);
        $this->openSearchItemsPerPage = new Extension\OpenSearchItemsPerPage();
    }

    public function testEmptyOpenSearchItemsPerPageShouldHaveNoExtensionElements() {
        $this->assertTrue(is_array($this->openSearchItemsPerPage->extensionElements));
        $this->assertTrue(count($this->openSearchItemsPerPage->extensionElements) == 0);
    }

    public function testEmptyOpenSearchItemsPerPageShouldHaveNoExtensionAttributes() {
        $this->assertTrue(is_array($this->openSearchItemsPerPage->extensionAttributes));
        $this->assertTrue(count($this->openSearchItemsPerPage->extensionAttributes) == 0);
    }

    public function testSampleOpenSearchItemsPerPageShouldHaveNoExtensionElements() {
        $this->openSearchItemsPerPage->transferFromXML($this->openSearchItemsPerPageText);
        $this->assertTrue(is_array($this->openSearchItemsPerPage->extensionElements));
        $this->assertTrue(count($this->openSearchItemsPerPage->extensionElements) == 0);
    }

    public function testSampleOpenSearchItemsPerPageShouldHaveNoExtensionAttributes() {
        $this->openSearchItemsPerPage->transferFromXML($this->openSearchItemsPerPageText);
        $this->assertTrue(is_array($this->openSearchItemsPerPage->extensionAttributes));
        $this->assertTrue(count($this->openSearchItemsPerPage->extensionAttributes) == 0);
    }

    public function testNormalOpenSearchItemsPerPageShouldHaveNoExtensionElements() {
        $this->openSearchItemsPerPage->text = "200";

        $this->assertEquals("200", $this->openSearchItemsPerPage->text);

        $this->assertEquals(0, count($this->openSearchItemsPerPage->extensionElements));
        $newOpenSearchItemsPerPage = new Extension\OpenSearchItemsPerPage();
        $newOpenSearchItemsPerPage->transferFromXML($this->openSearchItemsPerPage->saveXML());
        $this->assertEquals(0, count($newOpenSearchItemsPerPage->extensionElements));
        $newOpenSearchItemsPerPage->extensionElements = array(
                new \Zend\GData\App\Extension\Element('foo', 'atom', null, 'bar'));
        $this->assertEquals(1, count($newOpenSearchItemsPerPage->extensionElements));
        $this->assertEquals("200", $newOpenSearchItemsPerPage->text);

        /* try constructing using magic factory */
        $gdata = new \Zend\GData\GData();
        $newOpenSearchItemsPerPage2 = $gdata->newOpenSearchItemsPerPage();
        $newOpenSearchItemsPerPage2->transferFromXML($newOpenSearchItemsPerPage->saveXML());
        $this->assertEquals(1, count($newOpenSearchItemsPerPage2->extensionElements));
        $this->assertEquals("200", $newOpenSearchItemsPerPage2->text);
    }

    public function testEmptyOpenSearchItemsPerPageToAndFromStringShouldMatch() {
        $openSearchItemsPerPageXml = $this->openSearchItemsPerPage->saveXML();
        $newOpenSearchItemsPerPage = new Extension\OpenSearchItemsPerPage();
        $newOpenSearchItemsPerPage->transferFromXML($openSearchItemsPerPageXml);
        $newOpenSearchItemsPerPageXml = $newOpenSearchItemsPerPage->saveXML();
        $this->assertTrue($openSearchItemsPerPageXml == $newOpenSearchItemsPerPageXml);
    }

    public function testOpenSearchItemsPerPageWithValueToAndFromStringShouldMatch() {
        $this->openSearchItemsPerPage->text = "200";
        $openSearchItemsPerPageXml = $this->openSearchItemsPerPage->saveXML();
        $newOpenSearchItemsPerPage = new Extension\OpenSearchItemsPerPage();
        $newOpenSearchItemsPerPage->transferFromXML($openSearchItemsPerPageXml);
        $newOpenSearchItemsPerPageXml = $newOpenSearchItemsPerPage->saveXML();
        $this->assertTrue($openSearchItemsPerPageXml == $newOpenSearchItemsPerPageXml);
        $this->assertEquals("200", $this->openSearchItemsPerPage->text);
    }

    public function testExtensionAttributes() {
        $extensionAttributes = $this->openSearchItemsPerPage->extensionAttributes;
        $extensionAttributes['foo1'] = array('name'=>'foo1', 'value'=>'bar');
        $extensionAttributes['foo2'] = array('name'=>'foo2', 'value'=>'rab');
        $this->openSearchItemsPerPage->extensionAttributes = $extensionAttributes;
        $this->assertEquals('bar', $this->openSearchItemsPerPage->extensionAttributes['foo1']['value']);
        $this->assertEquals('rab', $this->openSearchItemsPerPage->extensionAttributes['foo2']['value']);
        $openSearchItemsPerPageXml = $this->openSearchItemsPerPage->saveXML();
        $newOpenSearchItemsPerPage = new Extension\OpenSearchItemsPerPage();
        $newOpenSearchItemsPerPage->transferFromXML($openSearchItemsPerPageXml);
        $this->assertEquals('bar', $newOpenSearchItemsPerPage->extensionAttributes['foo1']['value']);
        $this->assertEquals('rab', $newOpenSearchItemsPerPage->extensionAttributes['foo2']['value']);
    }

    public function testConvertFullOpenSearchItemsPerPageToAndFromString() {
        $this->openSearchItemsPerPage->transferFromXML($this->openSearchItemsPerPageText);
        $this->assertEquals("25", $this->openSearchItemsPerPage->text);
    }

}
