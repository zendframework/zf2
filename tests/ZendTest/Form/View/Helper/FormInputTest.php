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
use Zend\Form\View\Helper\FormInput as FormInputHelper;

class FormInputTest extends CommonTestCase
{
    public function setUp()
    {
        $this->helper = new FormInputHelper();
        parent::setUp();
    }

    public function testRaisesExceptionWhenNameIsNotPresentInElement()
    {
        $element = new Element();
        $this->setExpectedException('Zend\Form\Exception\DomainException', 'name');
        $this->helper->render($element);
    }

    public function testGeneratesTextInputTagWhenProvidedAnElementWithNoTypeAttribute()
    {
        $element = new Element('foo');
        $markup  = $this->helper->render($element);
        $this->assertContains('<input ', $markup);
        $this->assertContains('type="text"', $markup);
    }

    public function testGeneratesInputTagWithElementsTypeAttribute()
    {
        $element = new Element('foo');
        $element->setAttribute('type', 'email');
        $markup  = $this->helper->render($element);
        $this->assertContains('<input ', $markup);
        $this->assertContains('type="email"', $markup);
    }

    public function inputTypes()
    {
        return [
            ['text', 'assertContains'],
            ['button', 'assertContains'],
            ['checkbox', 'assertContains'],
            ['file', 'assertContains'],
            ['hidden', 'assertContains'],
            ['image', 'assertContains'],
            ['password', 'assertContains'],
            ['radio', 'assertContains'],
            ['reset', 'assertContains'],
            ['select', 'assertContains'],
            ['submit', 'assertContains'],
            ['color', 'assertContains'],
            ['date', 'assertContains'],
            ['datetime', 'assertContains'],
            ['datetime-local', 'assertContains'],
            ['email', 'assertContains'],
            ['month', 'assertContains'],
            ['number', 'assertContains'],
            ['range', 'assertContains'],
            ['search', 'assertContains'],
            ['tel', 'assertContains'],
            ['time', 'assertContains'],
            ['url', 'assertContains'],
            ['week', 'assertContains'],
            ['lunar', 'assertNotContains'],
            ['name', 'assertNotContains'],
            ['username', 'assertNotContains'],
            ['address', 'assertNotContains'],
            ['homepage', 'assertNotContains'],
        ];
    }

    /**
     * @dataProvider inputTypes
     */
    public function testOnlyAllowsValidInputTypes($type, $assertion)
    {
        $element = new Element('foo');
        $element->setAttribute('type', $type);
        $markup   = $this->helper->render($element);
        $expected = sprintf('type="%s"', $type);
        $this->$assertion($expected, $markup);
    }

    /**
     * @return array
     */
    public function validAttributes()
    {
        return [
            ['accept', 'assertContains'],
            ['accesskey', 'assertContains'],
            ['alt', 'assertContains'],
            ['autocomplete', 'assertContains'],
            ['autofocus', 'assertContains'],
            ['checked', 'assertContains'],
            ['class', 'assertContains'],
            ['contenteditable', 'assertContains'],
            ['contextmenu', 'assertContains'],
            ['dir', 'assertContains'],
            ['dirname', 'assertContains'],
            ['disabled', 'assertContains'],
            ['draggable', 'assertContains'],
            ['dropzone', 'assertContains'],
            ['form', 'assertContains'],
            ['formaction', 'assertContains'],
            ['formenctype', 'assertContains'],
            ['formmethod', 'assertContains'],
            ['formnovalidate', 'assertContains'],
            ['formtarget', 'assertContains'],
            ['height', 'assertContains'],
            ['hidden', 'assertContains'],
            ['id', 'assertContains'],
            ['lang', 'assertContains'],
            ['list', 'assertContains'],
            ['max', 'assertContains'],
            ['maxlength', 'assertContains'],
            ['min', 'assertContains'],
            ['multiple', 'assertContains'],
            ['name', 'assertContains'],
            ['onabort', 'assertContains'],
            ['onblur', 'assertContains'],
            ['oncanplay', 'assertContains'],
            ['oncanplaythrough', 'assertContains'],
            ['onchange', 'assertContains'],
            ['onclick', 'assertContains'],
            ['oncontextmenu', 'assertContains'],
            ['ondblclick', 'assertContains'],
            ['ondrag', 'assertContains'],
            ['ondragend', 'assertContains'],
            ['ondragenter', 'assertContains'],
            ['ondragleave', 'assertContains'],
            ['ondragover', 'assertContains'],
            ['ondragstart', 'assertContains'],
            ['ondrop', 'assertContains'],
            ['ondurationchange', 'assertContains'],
            ['onemptied', 'assertContains'],
            ['onended', 'assertContains'],
            ['onerror', 'assertContains'],
            ['onfocus', 'assertContains'],
            ['oninput', 'assertContains'],
            ['oninvalid', 'assertContains'],
            ['onkeydown', 'assertContains'],
            ['onkeypress', 'assertContains'],
            ['onkeyup', 'assertContains'],
            ['onload', 'assertContains'],
            ['onloadeddata', 'assertContains'],
            ['onloadedmetadata', 'assertContains'],
            ['onloadstart', 'assertContains'],
            ['onmousedown', 'assertContains'],
            ['onmousemove', 'assertContains'],
            ['onmouseout', 'assertContains'],
            ['onmouseover', 'assertContains'],
            ['onmouseup', 'assertContains'],
            ['onmousewheel', 'assertContains'],
            ['onpause', 'assertContains'],
            ['onplay', 'assertContains'],
            ['onplaying', 'assertContains'],
            ['onprogress', 'assertContains'],
            ['onratechange', 'assertContains'],
            ['onreadystatechange', 'assertContains'],
            ['onreset', 'assertContains'],
            ['onscroll', 'assertContains'],
            ['onseeked', 'assertContains'],
            ['onseeking', 'assertContains'],
            ['onselect', 'assertContains'],
            ['onshow', 'assertContains'],
            ['onstalled', 'assertContains'],
            ['onsubmit', 'assertContains'],
            ['onsuspend', 'assertContains'],
            ['ontimeupdate', 'assertContains'],
            ['onvolumechange', 'assertContains'],
            ['onwaiting', 'assertContains'],
            ['role', 'assertContains'],
        ];
    }

    public function validAttributes2()
    {
        return [
            ['pattern', 'assertContains'],
            ['placeholder', 'assertContains'],
            ['readonly', 'assertContains'],
            ['required', 'assertContains'],
            ['size', 'assertContains'],
            ['spellcheck', 'assertContains'],
            ['src', 'assertContains'],
            ['step', 'assertContains'],
            ['style', 'assertContains'],
            ['tabindex', 'assertContains'],
            ['title', 'assertContains'],
            ['value', 'assertContains'],
            ['width', 'assertContains'],
            ['xml:base', 'assertContains'],
            ['xml:lang', 'assertContains'],
            ['xml:space', 'assertContains'],
            ['data-some-key', 'assertContains'],
            ['option', 'assertNotContains'],
            ['optgroup', 'assertNotContains'],
            ['arbitrary', 'assertNotContains'],
            ['meta', 'assertNotContains'],
            ['role', 'assertContains'],
        ];
    }

    public function getCompleteElement()
    {
        $element = new Element('foo');
        $element->setAttributes([
            'accept'             => 'value',
            'accesskey'          => 'value',
            'alt'                => 'value',
            'autocomplete'       => 'postal-code',
            'autofocus'          => 'autofocus',
            'checked'            => 'checked',
            'class'              => 'value',
            'contenteditable'    => 'value',
            'contextmenu'        => 'value',
            'dir'                => 'value',
            'dirname'            => 'value',
            'disabled'           => 'disabled',
            'draggable'          => 'value',
            'dropzone'           => 'value',
            'form'               => 'value',
            'formaction'         => 'value',
            'formenctype'        => 'value',
            'formmethod'         => 'value',
            'formnovalidate'     => 'value',
            'formtarget'         => 'value',
            'height'             => 'value',
            'hidden'             => 'value',
            'id'                 => 'value',
            'lang'               => 'value',
            'list'               => 'value',
            'max'                => 'value',
            'maxlength'          => 'value',
            'min'                => 'value',
            'multiple'           => 'multiple',
            'name'               => 'value',
            'onabort'            => 'value',
            'onblur'             => 'value',
            'oncanplay'          => 'value',
            'oncanplaythrough'   => 'value',
            'onchange'           => 'value',
            'onclick'            => 'value',
            'oncontextmenu'      => 'value',
            'ondblclick'         => 'value',
            'ondrag'             => 'value',
            'ondragend'          => 'value',
            'ondragenter'        => 'value',
            'ondragleave'        => 'value',
            'ondragover'         => 'value',
            'ondragstart'        => 'value',
            'ondrop'             => 'value',
            'ondurationchange'   => 'value',
            'onemptied'          => 'value',
            'onended'            => 'value',
            'onerror'            => 'value',
            'onfocus'            => 'value',
            'oninput'            => 'value',
            'oninvalid'          => 'value',
            'onkeydown'          => 'value',
            'onkeypress'         => 'value',
            'onkeyup'            => 'value',
            'onload'             => 'value',
            'onloadeddata'       => 'value',
            'onloadedmetadata'   => 'value',
            'onloadstart'        => 'value',
            'onmousedown'        => 'value',
            'onmousemove'        => 'value',
            'onmouseout'         => 'value',
            'onmouseover'        => 'value',
            'onmouseup'          => 'value',
            'onmousewheel'       => 'value',
            'onpause'            => 'value',
            'onplay'             => 'value',
            'onplaying'          => 'value',
            'onprogress'         => 'value',
            'onratechange'       => 'value',
            'onreadystatechange' => 'value',
            'onreset'            => 'value',
            'onscroll'           => 'value',
            'onseeked'           => 'value',
            'onseeking'          => 'value',
            'onselect'           => 'value',
            'onshow'             => 'value',
            'onstalled'          => 'value',
            'onsubmit'           => 'value',
            'onsuspend'          => 'value',
            'ontimeupdate'       => 'value',
            'onvolumechange'     => 'value',
            'onwaiting'          => 'value',
            'pattern'            => 'value',
            'placeholder'        => 'value',
            'readonly'           => 'readonly',
            'required'           => 'required',
            'size'               => 'value',
            'spellcheck'         => 'value',
            'src'                => 'value',
            'step'               => 'value',
            'style'              => 'value',
            'tabindex'           => 'value',
            'title'              => 'value',
            'width'              => 'value',
            'wrap'               => 'value',
            'xml:base'           => 'value',
            'xml:lang'           => 'value',
            'xml:space'          => 'value',
            'data-some-key'      => 'value',
            'option'             => 'value',
            'optgroup'           => 'value',
            'arbitrary'          => 'value',
            'meta'               => 'value',
            'role'               => 'value',
        ]);
        $element->setValue('value');
        return $element;
    }

    /**
     * @dataProvider validAttributes
     * @return       void
     */
    public function testAllValidFormMarkupAttributesPresentInElementAreRendered($attribute, $assertion)
    {
        $element = $this->getCompleteElement();
        $markup  = $this->helper->render($element);
        switch ($attribute) {
            case 'value':
                $expect  = sprintf(' %s="%s"', $attribute, $element->getValue());
                break;
            default:
                $expect  = sprintf(' %s="%s"', $attribute, $element->getAttribute($attribute));
                break;
        }
        $this->$assertion($expect, $markup);
    }

    public function nonXhtmlDoctypes()
    {
        return [
            ['HTML4_STRICT'],
            ['HTML4_LOOSE'],
            ['HTML4_FRAMESET'],
            ['HTML5'],
        ];
    }

    /**
     * @dataProvider nonXhtmlDoctypes
     */
    public function testRenderingOmitsClosingSlashWhenDoctypeIsNotXhtml($doctype)
    {
        $element = new Element('foo');
        $this->renderer->doctype($doctype);
        $markup = $this->helper->render($element);
        $this->assertNotContains('/>', $markup);
    }

    public function xhtmlDoctypes()
    {
        return [
            ['XHTML11'],
            ['XHTML1_STRICT'],
            ['XHTML1_TRANSITIONAL'],
            ['XHTML1_FRAMESET'],
            ['XHTML1_RDFA'],
            ['XHTML_BASIC1'],
            ['XHTML5'],
        ];
    }

    /**
     * @dataProvider xhtmlDoctypes
     */
    public function testRenderingIncludesClosingSlashWhenDoctypeIsXhtml($doctype)
    {
        $element = new Element('foo');
        $this->renderer->doctype($doctype);
        $markup = $this->helper->render($element);
        $this->assertContains('/>', $markup);
    }

    /**
     * Data provider
     *
     * @return string[][]
     */
    public function booleanAttributeTypes()
    {
        return [
            ['autofocus', 'autofocus', ''],
            ['disabled', 'disabled', ''],
            ['multiple', 'multiple', ''],
            ['readonly', 'readonly', ''],
            ['required', 'required', ''],
            ['checked', 'checked', ''],
        ];
    }

    /**
     * @group ZF2-391
     * @dataProvider booleanAttributeTypes
     */
    public function testBooleanAttributeTypesAreRenderedCorrectly($attribute, $on, $off)
    {
        $element = new Element('foo');
        $element->setAttribute($attribute, true);
        $markup = $this->helper->render($element);
        $expect = sprintf('%s="%s"', $attribute, $on);
        $this->assertContains($expect, $markup, sprintf("Enabled value for %s should be '%s'; received %s", $attribute, $on, $markup));

        $element->setAttribute($attribute, false);
        $markup = $this->helper->render($element);
        $expect = sprintf('%s="%s"', $attribute, $off);

        if ($off !== '') {
            $this->assertContains($expect, $markup, sprintf("Disabled value for %s should be '%s'; received %s", $attribute, $off, $markup));
        } else {
            $this->assertNotContains($expect, $markup, sprintf("Disabled value for %s should not be rendered; received %s", $attribute, $markup));
        }

        // ZF2-391 : Ability to use non-boolean values that match expected end-value
        $element->setAttribute($attribute, $on);
        $markup = $this->helper->render($element);
        $expect = sprintf('%s="%s"', $attribute, $on);
        $this->assertContains($expect, $markup, sprintf("Enabled value for %s should be '%s'; received %s", $attribute, $on, $markup));

        $element->setAttribute($attribute, $off);
        $markup = $this->helper->render($element);
        $expect = sprintf('%s="%s"', $attribute, $off);

        if ($off !== '') {
            $this->assertContains($expect, $markup, sprintf("Disabled value for %s should be '%s'; received %s", $attribute, $off, $markup));
        } else {
            $this->assertNotContains($expect, $markup, sprintf("Disabled value for %s should not be rendered; received %s", $attribute, $markup));
        }
    }

    public function testInvokeProxiesToRender()
    {
        $element = new Element('foo');
        $markup  = $this->helper->__invoke($element);
        $this->assertContains('<input', $markup);
        $this->assertContains('name="foo"', $markup);
    }

    public function testInvokeWithNoElementChainsHelper()
    {
        $element = new Element('foo');
        $this->assertSame($this->helper, $this->helper->__invoke());
    }

    /**
     * @group ZF2-489
     */
    public function testCanTranslatePlaceholder()
    {
        $element = new Element('test');
        $element->setAttribute('placeholder', 'test');

        $mockTranslator = $this->getMock('Zend\I18n\Translator\Translator');

        $mockTranslator->expects($this->exactly(1))
                ->method('translate')
                ->will($this->returnValue('translated string'));

        $this->helper->setTranslator($mockTranslator);

        $this->assertTrue($this->helper->hasTranslator());

        $markup = $this->helper->__invoke($element);

        $this->assertContains('placeholder="translated&#x20;string"', $markup);
    }

    public function testCanTranslateTitle()
    {
        $element = new Element('test');
        $element->setAttribute('title', 'test');

        $mockTranslator = $this->getMock('Zend\I18n\Translator\Translator');

        $mockTranslator->expects($this->exactly(1))
                ->method('translate')
                ->with($this->equalTo('test'))
                ->will($this->returnValue('translated string'));

        $this->helper->setTranslator($mockTranslator);

        $this->assertTrue($this->helper->hasTranslator());

        $markup = $this->helper->__invoke($element);

        $this->assertContains('title="translated&#x20;string"', $markup);
    }

    /**
     * @group 7166
     */
    public function testPasswordValueShouldNotBeRendered()
    {
        $element = new Element('foo');
        $element->setAttribute('type', 'password');

        $markup  = $this->helper->__invoke($element);
        $this->assertContains('value=""', $markup);
    }
}
