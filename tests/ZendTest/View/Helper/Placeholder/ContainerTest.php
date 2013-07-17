<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_View
 */

namespace ZendTest\View\Helper\Placeholder;

use Zend\View\Helper\Placeholder\Container;

class ContainerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Container
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
        $this->helper = new Container(array());
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

    /**
     * @return void
     */
    public function testSetSetsASingleValue()
    {
        $this->helper['foo'] = 'bar';
        $this->helper['bar'] = 'baz';
        $this->assertEquals('bar', $this->helper['foo']);
        $this->assertEquals('baz', $this->helper['bar']);

        $this->helper->set('foo');
        $this->assertEquals(1, count($this->helper));
        $this->assertEquals('foo', $this->helper[0]);
    }

    /**
     * @return void
     */
    public function testGetValueReturnsScalarWhenOneElementRegistered()
    {
        $this->helper->set('foo');
        $this->assertEquals('foo', $this->helper->getValue());
    }

    /**
     * @return void
     */
    public function testGetValueReturnsArrayWhenMultipleValuesPresent()
    {
        $this->helper['foo'] = 'bar';
        $this->helper['bar'] = 'baz';
        $expected = array('foo' => 'bar', 'bar' => 'baz');
        $return   = $this->helper->getValue();
        $this->assertEquals($expected, $return);
    }

    /**
     * @return void
     */
    public function testPrefixAccesorsWork()
    {
        $this->assertEquals('', $this->helper->getPrefix());
        $this->helper->setPrefix('<ul><li>');
        $this->assertEquals('<ul><li>', $this->helper->getPrefix());
    }

    /**
     * @return void
     */
    public function testSetPrefixImplementsFluentInterface()
    {
        $result = $this->helper->setPrefix('<ul><li>');
        $this->assertSame($this->helper, $result);
    }

    /**
     * @return void
     */
    public function testPostfixAccesorsWork()
    {
        $this->assertEquals('', $this->helper->getPostfix());
        $this->helper->setPostfix('</li></ul>');
        $this->assertEquals('</li></ul>', $this->helper->getPostfix());
    }

    /**
     * @return void
     */
    public function testSetPostfixImplementsFluentInterface()
    {
        $result = $this->helper->setPostfix('</li></ul>');
        $this->assertSame($this->helper, $result);
    }

    /**
     * @return void
     */

    public function testPrependImplementsFluentInterface()
    {
        $result = $this->helper->prepend( 'test' );
        $this->assertSame($this->helper, $result);
    }

    /**
     * @return void
     */
    public function testSetImplementsFluentInterface()
    {
        $result = $this->helper->set( 'test' );
        $this->assertSame($this->helper, $result);
    }


    /**
     * @return void
     */
    public function testSeparatorAccesorsWork()
    {
        $this->assertEquals('', $this->helper->getSeparator());
        $this->helper->setSeparator('</li><li>');
        $this->assertEquals('</li><li>', $this->helper->getSeparator());
    }

    /**
     * @return void
     */
    public function testSetSeparatorImplementsFluentInterface()
    {
        $result = $this->helper->setSeparator('</li><li>');
        $this->assertSame($this->helper, $result);
    }

    /**
     * @return void
     */
    public function testIndentAccesorsWork()
    {
        $this->assertEquals('', $this->helper->getIndent());
        $this->helper->setIndent('    ');
        $this->assertEquals('    ', $this->helper->getIndent());
        $this->helper->setIndent(5);
        $this->assertEquals('     ', $this->helper->getIndent());
    }

    /**
     * @return void
     */
    public function testSetIndentImplementsFluentInterface()
    {
        $result = $this->helper->setIndent('    ');
        $this->assertSame($this->helper, $result);
    }

    /**
     * @return void
     */
    public function testCapturingToPlaceholderStoresContent()
    {
        $this->helper->captureStart();
        echo 'This is content intended for capture';
        $this->helper->captureEnd();

        $value = $this->helper->getValue();
        $this->assertContains('This is content intended for capture', $value);
    }

    /**
     * @return void
     */
    public function testCapturingToPlaceholderAppendsContent()
    {
        $this->helper[] = 'foo';
        $originalCount = count($this->helper);

        $this->helper->captureStart();
        echo 'This is content intended for capture';
        $this->helper->captureEnd();

        $this->assertEquals($originalCount + 1, count($this->helper));

        $value     = $this->helper->getValue();
        $keys      = array_keys($value);
        $lastIndex = array_pop($keys);
        $this->assertEquals('foo', $value[$lastIndex - 1]);
        $this->assertContains('This is content intended for capture', $value[$lastIndex]);
    }

    /**
     * @return void
     */
    public function testCapturingToPlaceholderUsingPrependPrependsContent()
    {
        $this->helper[] = 'foo';
        $originalCount = count($this->helper);

        $this->helper->captureStart('PREPEND');
        echo 'This is content intended for capture';
        $this->helper->captureEnd();

        $this->assertEquals($originalCount + 1, count($this->helper));

        $value     = $this->helper->getValue();
        $keys      = array_keys($value);
        $lastIndex = array_pop($keys);
        $this->assertEquals('foo', $value[$lastIndex]);
        $this->assertContains('This is content intended for capture', $value[$lastIndex - 1]);
    }

    /**
     * @return void
     */
    public function testCapturingToPlaceholderUsingSetOverwritesContent()
    {
        $this->helper[] = 'foo';
        $this->helper->captureStart('SET');
        echo 'This is content intended for capture';
        $this->helper->captureEnd();

        $this->assertEquals(1, count($this->helper));

        $value = $this->helper->getValue();
        $this->assertContains('This is content intended for capture', $value);
    }

    /**
     * @return void
     */
    public function testCapturingToPlaceholderKeyUsingSetCapturesContent()
    {
        $this->helper->captureStart('SET', 'key');
        echo 'This is content intended for capture';
        $this->helper->captureEnd();

        $this->assertEquals(1, count($this->helper));
        $this->assertTrue(isset($this->helper['key']));
        $value = $this->helper['key'];
        $this->assertContains('This is content intended for capture', $value);
    }

    /**
     * @return void
     */
    public function testCapturingToPlaceholderKeyUsingSetReplacesContentAtKey()
    {
        $this->helper['key'] = 'Foobar';
        $this->helper->captureStart('SET', 'key');
        echo 'This is content intended for capture';
        $this->helper->captureEnd();

        $this->assertEquals(1, count($this->helper));
        $this->assertTrue(isset($this->helper['key']));
        $value = $this->helper['key'];
        $this->assertContains('This is content intended for capture', $value);
    }

    /**
     * @return void
     */
    public function testCapturingToPlaceholderKeyUsingAppendAppendsContentAtKey()
    {
        $this->helper['key'] = 'Foobar ';
        $this->helper->captureStart('APPEND', 'key');
        echo 'This is content intended for capture';
        $this->helper->captureEnd();

        $this->assertEquals(1, count($this->helper));
        $this->assertTrue(isset($this->helper['key']));
        $value = $this->helper['key'];
        $this->assertContains('Foobar This is content intended for capture', $value);
    }

    /**
     * @return void
     */
    public function testNestedCapturesThrowsException()
    {
        $this->helper[] = 'foo';
        $caught = false;
        try {
            $this->helper->captureStart('SET');
                $this->helper->captureStart('SET');
                $this->helper->captureEnd();
            $this->helper->captureEnd();
        } catch (\Exception $e) {
            $this->helper->captureEnd();
            $caught = true;
        }

        $this->assertTrue($caught, 'Nested captures should throw exceptions');
    }

    /**
     * @return void
     */
    public function testToStringWithNoModifiersAndSingleValueReturnsValue()
    {
        $this->helper->set('foo');
        $value = $this->helper->toString();
        $this->assertEquals($this->helper->getValue(), $value);
    }

    /**
     * @return void
     */
    public function testToStringWithModifiersAndSingleValueReturnsFormattedValue()
    {
        $this->helper->set('foo');
        $this->helper->setPrefix('<li>')
                        ->setPostfix('</li>');
        $value = $this->helper->toString();
        $this->assertEquals('<li>foo</li>', $value);
    }

    /**
     * @return void
     */
    public function testToStringWithNoModifiersAndCollectionReturnsImplodedString()
    {
        $this->helper[] = 'foo';
        $this->helper[] = 'bar';
        $this->helper[] = 'baz';
        $value = $this->helper->toString();
        $this->assertEquals('foobarbaz', $value);
    }

    /**
     * @return void
     */
    public function testToStringWithModifiersAndCollectionReturnsFormattedString()
    {
        $this->helper[] = 'foo';
        $this->helper[] = 'bar';
        $this->helper[] = 'baz';
        $this->helper->setPrefix('<ul><li>')
                        ->setSeparator('</li><li>')
                        ->setPostfix('</li></ul>');
        $value = $this->helper->toString();
        $this->assertEquals('<ul><li>foo</li><li>bar</li><li>baz</li></ul>', $value);
    }

    /**
     * @return void
     */
    public function testToStringWithModifiersAndCollectionReturnsFormattedStringWithIndentation()
    {
        $this->helper[] = 'foo';
        $this->helper[] = 'bar';
        $this->helper[] = 'baz';
        $this->helper->setPrefix('<ul><li>')
                        ->setSeparator('</li>' . PHP_EOL . '<li>')
                        ->setPostfix('</li></ul>')
                        ->setIndent('    ');
        $value = $this->helper->toString();
        $expectedValue = '    <ul><li>foo</li>' . PHP_EOL . '    <li>bar</li>' . PHP_EOL . '    <li>baz</li></ul>';
        $this->assertEquals($expectedValue, $value);
    }

    /**
     * @return void
     */
    public function test__toStringProxiesToToString()
    {
        $this->helper[] = 'foo';
        $this->helper[] = 'bar';
        $this->helper[] = 'baz';
        $this->helper->setPrefix('<ul><li>')
                        ->setSeparator('</li><li>')
                        ->setPostfix('</li></ul>');
        $value = $this->helper->__toString();
        $this->assertEquals('<ul><li>foo</li><li>bar</li><li>baz</li></ul>', $value);
    }

    /**
     * @return void
     */
    public function testPrependPushesValueToTopOfContainer()
    {
        $this->helper['foo'] = 'bar';
        $this->helper->prepend('baz');

        $expected = array('baz', 'foo' => 'bar');
        $array = $this->helper->getArrayCopy();
        $this->assertSame($expected, $array);
    }

    public function testIndentationIsHonored()
    {
        $this->helper->setIndent(4)
                        ->setPrefix("<ul>\n    <li>")
                        ->setSeparator("</li>\n    <li>")
                        ->setPostfix("</li>\n</ul>");
        $this->helper->append('foo');
        $this->helper->append('bar');
        $this->helper->append('baz');
        $string = $this->helper->toString();

        $lis = substr_count($string, "\n        <li>");
        $this->assertEquals(3, $lis);
        $this->assertTrue((strstr($string, "    <ul>\n")) ? true : false, $string);
        $this->assertTrue((strstr($string, "\n    </ul>")) ? true : false);
    }
}
