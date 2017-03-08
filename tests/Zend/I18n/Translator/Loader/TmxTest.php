<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_I18n
 */

namespace ZendTest\I18n\Translator\Loader;

use PHPUnit_Framework_TestCase as TestCase;
use Locale;
use Zend\I18n\Translator\Translator;
use Zend\I18n\Translator\Loader\Tmx as TmxLoader;

class TmxTest extends TestCase
{
    protected $testFilesDir;
    protected $originalLocale;

    public function setUp()
    {
        $this->originalLocale = Locale::getDefault();
        Locale::setDefault('en_EN');

        $this->testFilesDir = realpath(__DIR__ . '/../_files');
    }

    public function tearDown()
    {
        Locale::setDefault($this->originalLocale);
    }

    public function testLoaderFailsToLoadMissingFile()
    {
        $loader = new TmxLoader();
        $this->setExpectedException('Zend\I18n\Exception\InvalidArgumentException', 'Could not open file');
        $loader->load('missing', 'en_EN');
    }

    public function testLoaderFailsToLoadBadFile()
    {
        $loader = new TmxLoader();
        $this->setExpectedException('Zend\I18n\Exception\InvalidArgumentException',
                                    'is not a valid tmx file');
        $loader->load($this->testFilesDir . '/failed.tmx', 'en_EN');
    }

    public function testLoaderLoadsEmptyFile()
    {
        $loader = new TmxLoader();
        $domain = $loader->load($this->testFilesDir . '/translation_empty.tmx', 'en_EN');
        $this->assertInstanceOf('Zend\I18n\Translator\TextDomain', $domain);
    }

    public function testLoaderReturnsValidTextDomain()
    {
        $loader = new TmxLoader();
        $textDomain = $loader->load($this->testFilesDir . '/translation_en.tmx', 'en_EN');

        $this->assertEquals('Message 1 (en)', $textDomain['Message 1']);
        $this->assertEquals('Message 4 (en)', $textDomain['Message 4']);
    }
}
