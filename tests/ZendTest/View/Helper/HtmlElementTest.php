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

use Zend\View\Renderer\PhpRenderer as View;
use ZendTest\View\Helper\TestAsset\OpeningTagHelper;

/**
 * @category   Zend
 * @package    Zend_View
 * @subpackage UnitTests
 * @group      Zend_View
 * @group      Zend_View_Helper
 */
class HtmlElementTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Zend_View_Helper_HtmlPage
     */
    public $helper;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @access protected
     */
    protected function setUp()
    {
        $this->view   = new View();
        $this->helper = new OpeningTagHelper();
        $this->helper->setView($this->view);
    }

    public function tearDown()
    {
        unset($this->helper);
    }

    public function testOpeningTag()
    {
        $output = $this->helper->__invoke();
        $this->assertSame($output, '<div');
    }

    public function testOpeningTagWithAttributes(){
        $attr = array(
            'class' => array('alpha', 'bravo'),
            'id' => 'some-id'
        );
        $output = $this->helper->__invoke($attr);
        $this->assertSame($output, '<div class="alpha bravo" id="some-id"');
    }
}
