<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\InputFilter;

use Zend\InputFilter\InputFilterPluginManager;

/**
 * @group Zend_Stdlib
 */
class InputFilterManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var InputFilterPluginManager
     */
    protected $manager;

    public function setUp()
    {
        $this->manager = new InputFilterPluginManager();
    }

    public function testRegisteringInvalidElementRaisesException()
    {
        $this->setExpectedException('Zend\InputFilter\Exception\RuntimeException');
        $this->manager->setService('test', $this);
    }

    public function testLoadingInvalidElementRaisesException()
    {
        $this->manager->setInvokableClass('test', get_class($this));
        $this->setExpectedException('Zend\InputFilter\Exception\RuntimeException');
        $this->manager->get('test');
    }

    /**
     * @covers Zend\InputFilter\InputFilterPluginManager::validatePlugin
     */
    public function testAllowLoadingInstancesOfInputFilterInterface()
    {
        $inputFilter = $this->getMock('Zend\InputFilter\InputFilterInterface');

        $this->assertNull($this->manager->validatePlugin($inputFilter));
    }

    /**
     * @covers Zend\InputFilter\InputFilterPluginManager::validatePlugin
     */
    public function testAllowLoadingInstancesOfInputInterface()
    {
        $input = $this->getMock('Zend\InputFilter\InputInterface');

        $this->assertNull($this->manager->validatePlugin($input));
    }
}
