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
 * @package    Zend_Filter
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Filter\Compress;

use Zend\Filter\Compress\Bz2 as Bz2Compression;

/**
 * @category   Zend
 * @package    Zend_Filter
 * @subpackage UnitTests
 * @group      Zend_Filter
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Bz2Test extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        if (!extension_loaded('bz2')) {
            $this->markTestSkipped('This adapter needs the bz2 extension');
        }
    }

    public function tearDown()
    {
        if (file_exists(__DIR__ . '/../_files/compressed.bz2')) {
            unlink(__DIR__ . '/../_files/compressed.bz2');
        }
    }

    /**
     * Basic usage
     *
     * @return void
     */
    public function testBasicUsage()
    {
        if (version_compare(phpversion(), '5.4', '>=')) {
            $this->markTestIncomplete('Code to test is not compatible with PHP 5.4 ');
        }

        $filter  = new Bz2Compression();

        $content = $filter->compress('compress me');
        $this->assertNotEquals('compress me', $content);

        $content = $filter->decompress($content);
        $this->assertEquals('compress me', $content);
    }

    /**
     * Setting Options
     *
     * @return void
     */
    public function testBz2GetSetOptions()
    {
        $filter = new Bz2Compression();
        $this->assertEquals(array('block_size' => 4, 'archive' => null), $filter->getOptions());

        $this->assertEquals(4, $filter->getOptions('block_size'));

        $this->assertNull($filter->getOptions('nooption'));

        $filter->setOptions(array('block_size' => 6));
        $this->assertEquals(6, $filter->getOptions('block_size'));

        $filter->setOptions(array('archive' => 'test.txt'));
        $this->assertEquals('test.txt', $filter->getOptions('archive'));

        $filter->setOptions(array('nooption' => 0));
        $this->assertNull($filter->getOptions('nooption'));
    }

    /**
     * Setting Options through constructor
     *
     * @return void
     */
    public function testBz2GetSetOptionsInConstructor()
    {
        $filter2= new Bz2Compression(array('block_size' => 8));
        $this->assertEquals(array('block_size' => 8, 'archive' => null), $filter2->getOptions());
    }

    /**
     * Setting Blocksize
     *
     * @return void
     */
    public function testBz2GetSetBlocksize()
    {
        $filter = new Bz2Compression();
        $this->assertEquals(4, $filter->getBlocksize());
        $filter->setBlocksize(6);
        $this->assertEquals(6, $filter->getOptions('block_size'));

        $this->setExpectedException('Zend\Filter\Exception\InvalidArgumentException', 'must be between');
        $filter->setBlocksize(15);
    }

    /**
     * Setting Archive
     *
     * @return void
     */
    public function testBz2GetSetArchive()
    {
        $filter = new Bz2Compression();
        $this->assertEquals(null, $filter->getArchive());
        $filter->setArchive('Testfile.txt');
        $this->assertEquals('Testfile.txt', $filter->getArchive());
        $this->assertEquals('Testfile.txt', $filter->getOptions('archive'));
    }

    /**
     * Setting Archive
     *
     * @return void
     */
    public function testBz2CompressToFile()
    {
        $filter   = new Bz2Compression();
        $archive = __DIR__ . '/../_files/compressed.bz2';
        $filter->setArchive($archive);

        $content = $filter->compress('compress me');
        $this->assertTrue($content);

        $filter2  = new Bz2Compression();
        $content2 = $filter2->decompress($archive);
        $this->assertEquals('compress me', $content2);

        $filter3 = new Bz2Compression();
        $filter3->setArchive($archive);
        $content3 = $filter3->decompress(null);
        $this->assertEquals('compress me', $content3);
    }

    /**
     * testing toString
     *
     * @return void
     */
    public function testBz2ToString()
    {
        $filter = new Bz2Compression();
        $this->assertEquals('Bz2', $filter->toString());
    }

    /**
     * Basic usage
     *
     * @return void
     */
    public function testBz2DecompressArchive()
    {
        $filter   = new Bz2Compression();
        $archive = __DIR__ . '/../_files/compressed.bz2';
        $filter->setArchive($archive);

        $content = $filter->compress('compress me');
        $this->assertTrue($content);

        $filter2  = new Bz2Compression();
        $content2 = $filter2->decompress($archive);
        $this->assertEquals('compress me', $content2);
    }
}
