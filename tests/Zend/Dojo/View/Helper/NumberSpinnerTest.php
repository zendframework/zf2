<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Dojo
 */

namespace ZendTest\Dojo\View\Helper;

use Zend\Dojo\View\Helper\NumberSpinner as NumberSpinnerHelper;
use Zend\Dojo\View\Helper\Dojo as DojoHelper;
use Zend\Json\Json;
use Zend\Registry;
use Zend\View;

/**
 * Test class for Zend_Dojo_View_Helper_NumberSpinner.
 *
 * @category   Zend
 * @package    Zend_Dojo
 * @subpackage UnitTests
 * @group      Zend_Dojo
 * @group      Zend_Dojo_View
 */
class NumberSpinnerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    public function setUp()
    {
        Registry::_unsetInstance();
        DojoHelper::setUseDeclarative();

        $this->view   = $this->getView();
        $this->helper = new NumberSpinnerHelper();
        $this->helper->setView($this->view);
    }

    public function getView()
    {
        $view = new View\Renderer\PhpRenderer();
        \Zend\Dojo\Dojo::enableView($view);
        return $view;
    }

    public function getElement()
    {
        return $this->helper->__invoke(
            'elementId',
            '5',
            array(
                'smallDelta' => '10',
                'min' => 9,
                'max' => 1550,
                'places' => 0,
                'required'    => true,
            ),
            array()
        );
    }

    public function testShouldAllowDeclarativeDijitCreation()
    {
        $html = $this->getElement();
        $this->assertRegexp('/<input[^>]*(dojoType="dijit.form.NumberSpinner")/', $html, $html);
    }

    public function testShouldAllowProgrammaticDijitCreation()
    {
        DojoHelper::setUseProgrammatic();
        $html = $this->getElement();
        $this->assertNotRegexp('/<input[^>]*(dojoType="dijit.form.NumberSpinner")/', $html);
        $this->assertNotNull($this->view->plugin('dojo')->getDijit('elementId'));
    }

    public function testShouldCreateTextInput()
    {
        $html = $this->getElement();
        $this->assertRegexp('/<input[^>]*(type="text")/', $html);
    }

    public function testShouldJsonEncodeConstraints()
    {
        $html = $this->getElement();
        if (!preg_match('/constraints="(.*?)(" )/', $html, $m)) {
            $this->fail('Did not serialize constraints');
        }
        $constraints = $m[1];
        $constraints = htmlspecialchars_decode($constraints, ENT_QUOTES);
        $constraints = str_replace("'", '"', $constraints);
        $constraints = Json::decode($constraints, JSON::TYPE_ARRAY);
        $this->assertTrue(is_array($constraints), var_export($m[1], 1));
        $this->assertTrue(array_key_exists('min', $constraints));
        $this->assertTrue(array_key_exists('max', $constraints));
        $this->assertTrue(array_key_exists('places', $constraints));
    }

    public function testInvalidConstraintsShouldBeStrippedPriorToRendering()
    {
        $html = $this->helper->__invoke(
            'foo',
            5,
            array (
                'constraints' => 'bogus',
            )
        );
        $this->assertNotContains('constraints="', $html);
    }
}
