<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_GData
 */

namespace ZendTest\GData\GApps;

use Zend\GData\GApps;
use Zend\GData\Extension;

/**
 * @category   Zend
 * @package    Zend_GData_GApps
 * @subpackage UnitTests
 * @group      Zend_GData
 * @group      Zend_GData_GApps
 */
class UserEntryTest extends \PHPUnit_Framework_TestCase
{

    public function setUp() {
        $this->entryText = file_get_contents(
                'Zend/GData/GApps/_files/UserEntryDataSample1.xml',
                true);
        $this->entry = new GApps\UserEntry();
    }

    private function verifyAllSamplePropertiesAreCorrect ($userEntry) {
        $this->assertEquals('https://apps-apis.google.com/a/feeds/example.com/user/2.0/SusanJones',
            $userEntry->id->text);
        $this->assertEquals('1970-01-01T00:00:00.000Z', $userEntry->updated->text);
        $this->assertEquals('http://schemas.google.com/g/2005#kind', $userEntry->category[0]->scheme);
        $this->assertEquals('http://schemas.google.com/apps/2006#user', $userEntry->category[0]->term);
        $this->assertEquals('text', $userEntry->title->type);
        $this->assertEquals('SusanJones', $userEntry->title->text);
        $this->assertEquals('self', $userEntry->getLink('self')->rel);
        $this->assertEquals('application/atom+xml', $userEntry->getLink('self')->type);
        $this->assertEquals('https://apps-apis.google.com/a/feeds/example.com/user/2.0/SusanJones', $userEntry->getLink('self')->href);
        $this->assertEquals('edit', $userEntry->getLink('edit')->rel);
        $this->assertEquals('application/atom+xml', $userEntry->getLink('edit')->type);
        $this->assertEquals('https://apps-apis.google.com/a/feeds/example.com/user/2.0/SusanJones', $userEntry->getLink('edit')->href);
        $this->assertEquals('SusanJones', $userEntry->login->username);
        $this->assertEquals('Jones', $userEntry->name->familyName);
        $this->assertEquals('Susan', $userEntry->name->givenName);
        $this->assertEquals('http://schemas.google.com/apps/2006#user.nicknames', $userEntry->getFeedLink('http://schemas.google.com/apps/2006#user.nicknames')->rel);
        $this->assertEquals('https://apps-apis.google.com/a/feeds/example.com/nickname/2.0?username=Susy-1321', $userEntry->getFeedLink('http://schemas.google.com/apps/2006#user.nicknames')->href);
        $this->assertEquals('http://schemas.google.com/apps/2006#user.emailLists', $userEntry->getFeedLink('http://schemas.google.com/apps/2006#user.emailLists')->rel);
        $this->assertEquals('https://apps-apis.google.com/a/feeds/example.com/emailList/2.0?recipient=us-sales@example.com', $userEntry->getFeedLink('http://schemas.google.com/apps/2006#user.emailLists')->href);
        $this->assertEquals('2048', $userEntry->quota->limit);
    }

    public function testEmptyEntryShouldHaveNoExtensionElements() {
        $this->assertTrue(is_array($this->entry->extensionElements));
        $this->assertTrue(count($this->entry->extensionElements) == 0);
    }

    public function testEmptyEntryShouldHaveNoExtensionAttributes() {
        $this->assertTrue(is_array($this->entry->extensionAttributes));
        $this->assertTrue(count($this->entry->extensionAttributes) == 0);
    }

    public function testSampleEntryShouldHaveNoExtensionElements() {
        $this->entry->transferFromXML($this->entryText);
        $this->assertTrue(is_array($this->entry->extensionElements));
        $this->assertTrue(count($this->entry->extensionElements) == 0);
    }

    public function testSampleEntryShouldHaveNoExtensionAttributes() {
        $this->entry->transferFromXML($this->entryText);
        $this->assertTrue(is_array($this->entry->extensionAttributes));
        $this->assertTrue(count($this->entry->extensionAttributes) == 0);
    }

    public function testEmptyUserEntryToAndFromStringShouldMatch() {
        $entryXml = $this->entry->saveXML();
        $newUserEntry = new GApps\UserEntry();
        $newUserEntry->transferFromXML($entryXml);
        $newUserEntryXml = $newUserEntry->saveXML();
        $this->assertTrue($entryXml == $newUserEntryXml);
    }

    public function testGetFeedLinkReturnsAllStoredEntriesWhenUsedWithNoParameters() {
        // Prepare test data
        $entry1 = new Extension\FeedLink();
        $entry1->rel = "first";
        $entry1->href= "foo";
        $entry2 = new Extension\FeedLink();
        $entry2->rel = "second";
        $entry2->href= "bar";
        $data = array($entry1, $entry2);

        // Load test data and run test
        $this->entry->feedLink = $data;
        $this->assertEquals(2, count($this->entry->feedLink));
    }

    public function testGetFeedLinkCanReturnEntriesByRelValue() {
        // Prepare test data
        $entry1 = new Extension\FeedLink();
        $entry1->rel = "first";
        $entry1->href= "foo";
        $entry2 = new Extension\FeedLink();
        $entry2->rel = "second";
        $entry2->href= "bar";
        $data = array($entry1, $entry2);

        // Load test data and run test
        $this->entry->feedLink = $data;
        $this->assertEquals($entry1, $this->entry->getFeedLink('first'));
        $this->assertEquals($entry2, $this->entry->getFeedLink('second'));
    }

    public function testSamplePropertiesAreCorrect () {
        $this->entry->transferFromXML($this->entryText);
        $this->verifyAllSamplePropertiesAreCorrect($this->entry);
    }

    public function testConvertUserEntryToAndFromString() {
        $this->entry->transferFromXML($this->entryText);
        $entryXml = $this->entry->saveXML();
        $newUserEntry = new GApps\UserEntry();
        $newUserEntry->transferFromXML($entryXml);
        $this->verifyAllSamplePropertiesAreCorrect($newUserEntry);
        $newUserEntryXml = $newUserEntry->saveXML();
        $this->assertEquals($entryXml, $newUserEntryXml);
    }

}
