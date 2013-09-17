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
use Zend\InputFilter\ValidationGroup\NoOpFilterIterator;

class NoOpFilterIteratorTest extends TestCase
{
    public function testStripNothing()
    {
        $arrayIterator   = new ArrayIterator(array('foo' => 'oneInput'));
        $inputCollection = $this->getMock('Zend\InputFilter\InputCollectionInterface');
        $inputCollection->expects($this->once())
                        ->method('getIterator')
                        ->will($this->returnValue($arrayIterator));

        $noOpFilterIterator = new NoOpFilterIterator($inputCollection);

        foreach ($noOpFilterIterator as $key => $element) {
            $this->assertEquals('foo', $key);
        }
    }
}
