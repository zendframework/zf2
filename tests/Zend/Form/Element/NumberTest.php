<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Form
 * @subpackage UnitTest
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Form\Element;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\Form\Element\Number as NumberElement;
use Zend\Form\Factory;

class NumberTest extends TestCase
{
    public function testCanInjectValidator()
    {
        $element   = new NumberElement();
        $validator = $this->getMock('Zend\Validator\ValidatorInterface');
        $element->addValidator($validator);
        $this->assertSame(array($validator), $element->getValidators());
    }

    public function testCanInjectMultipleValidators()
    {
        $element   = new NumberElement();
        $firstValidator = $this->getMock('Zend\Validator\ValidatorInterface');
        $secondValidator = $this->getMock('Zend\Validator\ValidatorInterface');
        $element->addValidator($firstValidator);
        $element->addValidator($secondValidator);
        $this->assertSame(array($firstValidator, $secondValidator), $element->getValidators());
    }

    public function testProvidesInputSpecificationThatIncludesValidator()
    {
        $element = new NumberElement();
        $validator = $this->getMock('Zend\Validator\ValidatorInterface');
        $element->addValidator($validator);

        $inputSpec = $element->getInputSpecification();
        $this->assertArrayHasKey('validators', $inputSpec);
        $this->assertInternalType('array', $inputSpec['validators']);
        $test = array_shift($inputSpec['validators']);
        $this->assertSame($validator, $test);
    }
}
