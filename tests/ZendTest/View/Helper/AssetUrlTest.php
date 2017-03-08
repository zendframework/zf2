<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_View
 */

namespace ZendTest\View\Helper;

use PhpUnit_Framework_TestCase as TestCase;
use Zend\View\Helper\AssetUrl;
use Zend\View\Exception;

/**
 * @category   Zend
 * @package    Zend_View
 * @subpackage UnitTests
 * @group      Zend_View
 * @group      Zend_View_Helper
 */
class AssetUrlTest extends TestCase
{

    public function testConstructorDoesNotThrowExceptionIfNoOptionsAreSpecified()
    {
        $assetUrlHelper = new AssetUrl();

        $this->assertInstanceOf(
            'Zend\View\Helper\AssetUrl',
            $assetUrlHelper
        );
    }

    /**
     * @expectedException \Zend\View\Exception\InvalidArgumentException
     */
    public function testConstructorThrowsZendViewExceptionInvalidArgumentExceptionIfOptionsAreNotSpecifiedAsAnArray()
    {
        $assetUrlHelper = new AssetUrl('arbitrary string');
    }

    /**
     * @expectedException \Zend\View\Exception\InvalidArgumentException
     */
    public function testRegisterAssetUrlThrowsZendViewExceptionInvalidArgumentExceptionIfKeyIsNotAString()
    {
        $assetUrlHelper = new AssetUrl();
        $assetUrlHelper->setAssetUrl(new \stdClass());
    }

    /**
     * @expectedException \Zend\View\Exception\InvalidArgumentException
     */
    public function testSetAssetUrlThrowsZendViewExceptionInvalidArgumentExceptionIfPrefixIsNotAString()
    {
        $assetUrlHelper = new AssetUrl();
        $assetUrlHelper->setAssetUrl(
            'scvbafrfvqwcn',
            new \stdClass()
        );
    }

    /**
     * @expectedException \Zend\View\Exception\InvalidArgumentException
     */
    public function testSetAssetUrlThrowsZendViewExceptionInvalidArgumentExceptionIfSuffixIsNotAString()
    {
        $assetUrlHelper = new AssetUrl();
        $assetUrlHelper->setAssetUrl(
            'scvbafrfvqwcn',
            'pefjwpefj',
            new \stdClass()
        );
    }

    /**
     * @expectedException \Zend\View\Exception\InvalidArgumentException
     */
    public function testInvokeThrowsZendViewExceptionInvalidArgumentExceptionIfFileIsNotAString()
    {
        $assetUrlHelper = new AssetUrl();
        $assetUrlHelper(new \stdClass());
    }

    public function testGetDefaultKeyReturnsFirstRegisteredAssetUrlIfNotExplicitlySpecified()
    {

        $assetUrlHelper = new AssetUrl();

        $key = 'opamama';

        $assetUrlHelper->setAssetUrl($key);

        $this->assertEquals(
            $key,
            $assetUrlHelper->getDefaultKey()
        );
    }

    /**
     * @expectedException \Zend\View\Exception\InvalidArgumentException
     */
    public function testSetDefaultKeyThrowsZendViewExceptionInvalidArgumentExceptionIfKeyIsNotAString()
    {
        $assetUrlHelper = new AssetUrl();
        $assetUrlHelper->setDefaultKey(new \stdClass());
    }

    public function testSetDefaultKeySetsDefaultKey()
    {
        $assetUrlHelper = new AssetUrl();

        $key = 'opamama';

        $assetUrlHelper->setDefaultKey($key);

        $this->assertEquals(
            $key,
            $assetUrlHelper->getDefaultKey()
        );
    }

    public function testConstructorSetsDefaultKey()
    {
        $key = 'opamama';

        $assetUrlHelper = new AssetUrl(array(
            'default_key' => $key,
        ));

        $this->assertEquals(
            $key,
            $assetUrlHelper->getDefaultKey()
        );
    }

    public function testInvokeReturnsFilenameIfNoAssetUrlsAreSet()
    {
        $assetUrlHelper = new AssetUrl();

        $file = 'css/styles.css';

        $this->assertEquals(
            $file,
            $assetUrlHelper($file)
        );
    }

    public function testInvokeReturnsFilenameWithDefaultAssetUrlIfNoKeyIsSpecified()
    {
        $assetUrlHelper = new AssetUrl();

        $key = 'opamama';
        $prefix = '/assets/';
        $suffix = '12345';

        $file = 'css/styles.css?456';

        $assetUrlHelper->setAssetUrl(
            $key,
            $prefix,
            $suffix
        );

        $this->assertEquals(
            '/assets/css/styles.css?456&12345',
            $assetUrlHelper($file)
        );

    }

    /**
     * @expectedException \Zend\View\Exception\InvalidArgumentException
     */
    public function testInvokeThrowsInvalidArgumentExceptionIfKeyIsNotSpecifiedAsString()
    {
        $assetUrlHelper = new AssetUrl();

        $file = 'css/styles.css';
        $key = new \stdClass();

        $assetUrlHelper(
            $file,
            $key
        );
    }

    /**
     * @expectedException \Zend\View\Exception\BadMethodCallException
     */
    public function testInvokeThrowsBadMethodCallExceptionIfKeyIsSpecifiedForWhichNoAssetUrlHasBeenSet()
    {
        $assetUrlHelper = new AssetUrl();

        $file = 'css/styles.css';
        $key = 'opamama';

        $assetUrlHelper(
            $file,
            $key
        );
    }

    public function testConstructorSetsAssetUrls()
    {

        $assetUrlHelper = new AssetUrl(array(
            'asset_urls' => array(
                'opa' => array(
                    'prefix' => 'assets/opa',
                    'suffix' => 'heinz',
                ),
            ),
        ));

        $this->assertEquals(
            'opa',
            $assetUrlHelper->getDefaultKey()
        );

        $this->assertEquals(
            'assets/opa/css/styles.css?heinz',
            $assetUrlHelper(
                'css/styles.css',
                'opa'
            )
        );
    }

    public function testInvokeLeftTrimsSlashOfAsset()
    {
        $assetUrlHelper = new AssetUrl(array(
            'asset_urls' => array(
                'opa' => array(
                    'prefix' => 'assets/opa/',
                ),
            ),
        ));

        $this->assertEquals(
            'assets/opa/js/opa.js',
            $assetUrlHelper(
                '/js/opa.js',
                'opa'
            )
        );
    }

}
