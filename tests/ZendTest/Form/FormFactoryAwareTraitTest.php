<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Form
 */

namespace ZendTest\Form;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\Form\Factory;

/**
 * @deprecated As of 2.2.0 <b>Form Factory</b> should be set/get via <b>Form Manager</b>
 * @requires PHP 5.4
 *
 * @group Zend_Form
 */
class FormFactoryAwareTraitTest extends TestCase
{
    public function testSetFormFactory()
    {
        $this->markTestSkipped(
            'Zend\Form\Factory has been deprecated in ZF as of 2.2.0, use Zend\Form\FormFactory instead'
        );
    }
}
