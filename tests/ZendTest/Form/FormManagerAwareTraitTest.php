<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Form;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\Form\FormManager;

/**
 * @group    Zend_Form
 * @requires PHP 5.4
 */
class FormManagerAwareTraitTest extends TestCase
{
    public function testSetFormManager()
    {
        $object = $this->getObjectForTrait('\Zend\Form\FormManagerAwareTrait');

        $this->assertAttributeEquals(null, 'formManager', $object);

        $formManager = new FormManager;

        $object->setFormManager($formManager);

        $this->assertAttributeEquals($formManager, 'formManager', $object);
    }

    public function testGetFormManager()
    {
        $object = $this->getObjectForTrait('\Zend\Form\FormManagerAwareTrait');

        // The instance is lazy loaded
        $this->assertInstanceOf('Zend\Form\FormManager', $object->getFormManager());

        $formManager = new FormManager;

        $object->setFormManager($formManager);

        $this->assertEquals($formManager, $object->getFormManager());
    }
}
