<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_InputFilter
 */

namespace ZendTest\InputFilter\ValidationGroup;

use ArrayIterator;
use PHPUnit_Framework_TestCase as TestCase;
use Zend\InputFilter\ValidationGroup\CallbackFilterIterator;

class CallbackFilterIteratorTest extends TestCase
{
    public function testStripOnlyValuesNotAcceptedInCallable()
    {
        $arrayIterator   = new ArrayIterator(array('foo' => 'oneInput', 'bar' => 'anotherInput'));
        $inputCollection = $this->getMock('Zend\InputFilter\InputCollectionInterface');
        $inputCollection->expects($this->once())
                        ->method('getIterator')
                        ->will($this->returnValue($arrayIterator));

        $arrayFilterIterator = new CallbackFilterIterator($inputCollection, function($value, $key) {
            return $key === 'bar';
        });

        $count = 0;

        foreach ($arrayFilterIterator as $key => $element) {
            $this->assertEquals('bar', $key);
            $this->assertNotEquals('foo', $key);
            $count++;
        }

        $this->assertEquals(1, $count);
    }
}
