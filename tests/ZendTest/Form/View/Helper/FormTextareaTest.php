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
use Zend\Form\View\Helper\FormTextarea as FormTextareaHelper;

class FormTextareaTest extends CommonTestCase
{
    public function setUp()
    {
        $this->helper = new FormTextareaHelper();
        parent::setUp();
    }

    public function testRaisesExceptionWhenNameIsNotPresentInElement()
    {
        $element = new Element();
        $this->setExpectedException('Zend\Form\Exception\DomainException', 'name');
        $this->helper->render($element);
    }

    public function testGeneratesEmptyTextareaWhenNoValueAttributePresent()
    {
        $element = new Element('foo');
        $markup  = $this->helper->render($element);
        $this->assertRegexp('#<textarea.*?></textarea>#', $markup);
    }

    public function validAttributes()
    {
        return [
            ['accesskey', 'assertContains'],
            ['class', 'assertContains'],
            ['contenteditable', 'assertContains'],
            ['contextmenu', 'assertContains'],
            ['dir', 'assertContains'],
            ['draggable', 'assertContains'],
            ['dropzone', 'assertContains'],
            ['hidden', 'assertContains'],
            ['id', 'assertContains'],
            ['lang', 'assertContains'],
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
            ['spellcheck', 'assertContains'],
            ['style', 'assertContains'],
            ['tabindex', 'assertContains'],
            ['title', 'assertContains'],
            ['xml:base', 'assertContains'],
            ['xml:lang', 'assertContains'],
            ['xml:space', 'assertContains'],
            ['data-some-key', 'assertContains'],
            ['autofocus', 'assertContains'],
            ['cols', 'assertContains'],
            ['dirname', 'assertContains'],
            ['disabled', 'assertContains'],
            ['form', 'assertContains'],
            ['maxlength', 'assertContains'],
            ['name', 'assertContains'],
            ['placeholder', 'assertContains'],
            ['readonly', 'assertContains'],
            ['required', 'assertContains'],
            ['rows', 'assertContains'],
            ['wrap', 'assertContains'],
            ['content', 'assertNotContains'],
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
            'accesskey'          => 'value',
            'class'              => 'value',
            'contenteditable'    => 'value',
            'contextmenu'        => 'value',
            'dir'                => 'value',
            'draggable'          => 'value',
            'dropzone'           => 'value',
            'hidden'             => 'value',
            'id'                 => 'value',
            'lang'               => 'value',
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
            'spellcheck'         => 'value',
            'style'              => 'value',
            'tabindex'           => 'value',
            'title'              => 'value',
            'xml:base'           => 'value',
            'xml:lang'           => 'value',
            'xml:space'          => 'value',
            'data-some-key'      => 'value',
            'autofocus'          => 'autofocus',
            'cols'               => 'value',
            'dirname'            => 'value',
            'disabled'           => 'disabled',
            'form'               => 'value',
            'maxlength'          => 'value',
            'name'               => 'value',
            'placeholder'        => 'value',
            'readonly'           => 'readonly',
            'required'           => 'required',
            'rows'               => 'value',
            'wrap'               => 'value',
            'content'            => 'value',
            'option'             => 'value',
            'optgroup'           => 'value',
            'arbitrary'          => 'value',
            'meta'               => 'value',
            'role'               => 'value',
        ]);
        return $element;
    }

    /**
     * @dataProvider validAttributes
     */
    public function testAllValidFormMarkupAttributesPresentInElementAreRendered($attribute, $assertion)
    {
        $element = $this->getCompleteElement();
        $markup  = $this->helper->render($element);
        $expect  = sprintf('%s="%s"', $attribute, $element->getAttribute($attribute));
        $this->$assertion($expect, $markup);
    }

    public function booleanAttributeTypes()
    {
        return [
            ['autofocus', 'autofocus', ''],
            ['disabled', 'disabled', ''],
            ['readonly', 'readonly', ''],
            ['required', 'required', ''],
        ];
    }

    /**
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
    }

    public function testRendersValueAttributeAsTextareaContent()
    {
        $element = new Element('foo');
        $element->setAttribute('value', 'Initial content');
        $markup = $this->helper->render($element);
        $this->assertContains('>Initial content</textarea>', $markup);
    }

    public function testInvokeProxiesToRender()
    {
        $element = new Element('foo');
        $markup  = $this->helper->__invoke($element);
        $this->assertContains('<textarea', $markup);
        $this->assertContains('name="foo"', $markup);
    }

    public function testInvokeWithNoElementChainsHelper()
    {
        $element = new Element('foo');
        $this->assertSame($this->helper, $this->helper->__invoke());
    }
}
