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

use Zend\View\Helper\Placeholder as PlaceholderHelper;
use Zend\View\Renderer\PhpRenderer as View;

class RenderToPlaceholderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var View
     */
    protected $view;

    public function setUp()
    {
        $this->view = new View();
        $this->view->resolver()->addPath(__DIR__.'/_files/scripts/');
    }

    public function testDefaultEmpty()
    {
        $this->view->plugin('renderToPlaceholder')->__invoke('rendertoplaceholderscript.phtml', 'fooPlaceholder');
        $placeholder = new PlaceholderHelper();
        $this->assertEquals("Foo Bar" . "\n", $placeholder->__invoke('fooPlaceholder')->getValue());
    }

}
