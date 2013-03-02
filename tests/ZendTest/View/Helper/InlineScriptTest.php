<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_View
 */

namespace ZendTest\View\Helper;

use Zend\View\Helper\InlineScript;
use Zend\View\Helper\Placeholder\Registry;

class InlineScriptTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    protected $basePath;

    /**
     * @var InlineScript
     */
    protected $helper;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    public function setUp()
    {
        Registry::unsetRegistry();
        $this->basePath = __DIR__ . '/_files/modules';
        $this->helper = new InlineScript();
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->helper);
    }

    public function testNamespaceRegisteredInPlaceholderRegistryAfterInstantiation()
    {
        $registry = Registry::getRegistry();
        if ($registry->containerExists('Zend_View_Helper_InlineScript')) {
            $registry->deleteContainer('Zend_View_Helper_InlineScript');
        }
        $this->assertFalse($registry->containerExists('Zend_View_Helper_InlineScript'));
        $helper = new InlineScript();
        $this->assertTrue($registry->containerExists('Zend_View_Helper_InlineScript'));
    }

    public function testInlineScriptReturnsObjectInstance()
    {
        $placeholder = $this->helper->__invoke();
        $this->assertTrue($placeholder instanceof InlineScript);
    }
}
