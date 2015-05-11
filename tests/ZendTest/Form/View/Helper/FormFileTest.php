<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Form\View\Helper;

use Zend\Form\Element;
use Zend\Form\View\Helper\FormFile as FormFileHelper;

class FormFileTest extends CommonTestCase
{
    /**
     * @return void
     */
    public function setUp()
    {
        $this->helper = new FormFileHelper();
        parent::setUp();
    }

    /**
     * @return void
     */
    public function testRaisesExceptionWhenNameIsNotPresentInElement()
    {
        $element = new Element\File();
        $this->setExpectedException('Zend\Form\Exception\DomainException', 'name');
        $this->helper->render($element);
    }

    /**
     * @return void
     */
    public function testGeneratesFileInputTagWithElement()
    {
        $element = new Element\File('foo');
        $markup  = $this->helper->render($element);
        $this->assertContains('<input ', $markup);
        $this->assertContains('type="file"', $markup);
    }

    /**
     * @return void
     */
    public function testGeneratesFileInputTagRegardlessOfElementType()
    {
        $element = new Element\File('foo');
        $element->setAttribute('type', 'email');
        $markup  = $this->helper->render($element);
        $this->assertContains('<input ', $markup);
        $this->assertContains('type="file"', $markup);
    }

    /**
     * @return void
     */
    public function testRendersElementWithFileIgnoresValue()
    {
        $element = new Element\File('foo');
        $element->setValue([
            'tmp_name' => '/tmp/foofile',
            'name'     => 'foofile',
            'type'     => 'text',
            'size'     => 200,
            'error'    => 2,
        ]);
        $markup  = $this->helper->render($element);
        $this->assertContains('<input ', $markup);
        $this->assertContains('type="file"', $markup);
        $this->assertNotContains('value="', $markup);
    }

    /**
     * @return array
     */
    public function validAttributes()
    {
        return [
            ['name', 'assertContains'],
            ['accept', 'assertContains'],
            ['alt', 'assertNotContains'],
            ['autocomplete', 'assertNotContains'],
            ['autofocus', 'assertContains'],
            ['checked', 'assertNotContains'],
            ['dirname', 'assertNotContains'],
            ['disabled', 'assertContains'],
            ['form', 'assertContains'],
            ['formaction', 'assertNotContains'],
            ['formenctype', 'assertNotContains'],
            ['formmethod', 'assertNotContains'],
            ['formnovalidate', 'assertNotContains'],
            ['formtarget', 'assertNotContains'],
            ['height', 'assertNotContains'],
            ['list', 'assertNotContains'],
            ['max', 'assertNotContains'],
            ['maxlength', 'assertNotContains'],
            ['min', 'assertNotContains'],
            ['multiple', 'assertNotContains'],
            ['pattern', 'assertNotContains'],
            ['placeholder', 'assertNotContains'],
            ['readonly', 'assertNotContains'],
            ['required', 'assertContains'],
            ['size', 'assertNotContains'],
            ['src', 'assertNotContains'],
            ['step', 'assertNotContains'],
            ['width', 'assertNotContains'],
        ];
    }

    /**
     * @return Element\File
     */
    public function getCompleteElement()
    {
        $element = new Element\File('foo');
        $element->setAttributes([
            'accept'             => 'value',
            'alt'                => 'value',
            'autocomplete'       => 'on',
            'autofocus'          => 'autofocus',
            'checked'            => 'checked',
            'dirname'            => 'value',
            'disabled'           => 'disabled',
            'form'               => 'value',
            'formaction'         => 'value',
            'formenctype'        => 'value',
            'formmethod'         => 'value',
            'formnovalidate'     => 'value',
            'formtarget'         => 'value',
            'height'             => 'value',
            'id'                 => 'value',
            'list'               => 'value',
            'max'                => 'value',
            'maxlength'          => 'value',
            'min'                => 'value',
            'multiple'           => false,
            'name'               => 'value',
            'pattern'            => 'value',
            'placeholder'        => 'value',
            'readonly'           => 'readonly',
            'required'           => 'required',
            'size'               => 'value',
            'src'                => 'value',
            'step'               => 'value',
            'width'              => 'value',
        ]);
        $element->setValue('value');
        return $element;
    }

    /**
     * @dataProvider validAttributes
     */
    public function testAllValidFormMarkupAttributesPresentInElementAreRendered($attribute, $assertion)
    {
        $element = $this->getCompleteElement();
        $markup  = $this->helper->render($element);
        switch ($attribute) {
            case 'value':
                $expect  = sprintf('%s="%s"', $attribute, $element->getValue());
                break;
            default:
                $expect  = sprintf('%s="%s"', $attribute, $element->getAttribute($attribute));
                break;
        }
        $this->$assertion($expect, $markup);
    }

    /**
     * @return void
     */
    public function testNameShouldHaveArrayNotationWhenMultipleIsSpecified()
    {
        $element = new Element\File('foo');
        $element->setAttribute('multiple', true);
        $markup = $this->helper->render($element);
        $this->assertRegexp('#<input[^>]*?(name="foo\&\#x5B\;\&\#x5D\;")#', $markup);
    }

    /**
     * @return void
     */
    public function testInvokeProxiesToRender()
    {
        $element = new Element\File('foo');
        $markup  = $this->helper->__invoke($element);
        $this->assertContains('<input', $markup);
        $this->assertContains('name="foo"', $markup);
        $this->assertContains('type="file"', $markup);
    }

    /**
     * @return void
     */
    public function testInvokeWithNoElementChainsHelper()
    {
        $this->assertSame($this->helper, $this->helper->__invoke());
    }
}
