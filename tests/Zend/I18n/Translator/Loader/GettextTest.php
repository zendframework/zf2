<?php

namespace ZendTest\I18n\Translator\Loader;

use PHPUnit_Framework_TestCase as TestCase;
use Locale;
use Zend\I18n\Translator\Translator;
use Zend\I18n\Translator\Loader\Gettext as GettextLoader;

class GettextTest extends TestCase
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
        $loader = new GettextLoader();
        $this->setExpectedException('Zend\I18n\Exception\InvalidArgumentException', 'Could not open file');
        $loader->load('missing', 'en_EN');
    }

    public function testLoaderFailsToLoadBadFile()
    {
        $loader = new GettextLoader();
        $this->setExpectedException('Zend\I18n\Exception\InvalidArgumentException',
                                    'is not a valid gettext file');
        $loader->load($this->testFilesDir . '/failed.mo', 'en_EN');
    }

    public function testLoaderLoadsEmptyFile()
    {
        $loader = new GettextLoader();
        $domain = $loader->load($this->testFilesDir . '/translation_empty.mo', 'en_EN');
        $this->assertInstanceOf('Zend\I18n\Translator\TextDomain', $domain);
    }

    public function testLoaderLoadsBigEndianFile()
    {
        $loader = new GettextLoader();
        $domain = $loader->load($this->testFilesDir . '/translation_bigendian.mo', 'en_EN');
        $this->assertInstanceOf('Zend\I18n\Translator\TextDomain', $domain);
    }

    public function testLoaderReturnsValidTextDomain()
    {
        $loader = new GettextLoader();
        $textDomain = $loader->load($this->testFilesDir . '/translation_en.mo', 'en_EN');

        $this->assertEquals('Message 1 (en)', $textDomain['Message 1']);
        $this->assertEquals('Message 4 (en)', $textDomain['Message 4']);
    }

    public function testLoaderLoadsPluralRules()
    {
        $loader     = new GettextLoader();
        $textDomain = $loader->load($this->testFilesDir . '/translation_en.mo', 'en_EN');

        $this->assertEquals(2, $textDomain->getPluralRule()->evaluate(0));
        $this->assertEquals(0, $textDomain->getPluralRule()->evaluate(1));
        $this->assertEquals(1, $textDomain->getPluralRule()->evaluate(2));
        $this->assertEquals(2, $textDomain->getPluralRule()->evaluate(10));
    }
}
