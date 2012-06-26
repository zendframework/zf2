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
 * @package    Zend_Validator
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Validator;

use Zend\Validator\AbstractValidator;
use Zend\Validator\Alpha;
use Zend\Validator\Between;
use Zend\Validator\StaticValidator;
use Zend\Validator\ValidatorPluginManager;
use Zend\Translator;

/**
 * @category   Zend
 * @package    Zend_Validator
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Validator
 */
class StaticValidatorTest extends \PHPUnit_Framework_TestCase
{
    /** @var Alpha */
    public $validator;

    /**
     * Whether an error occurred
     *
     * @var boolean
     */
    protected $errorOccurred = false;

    public function clearRegistry()
    {
        if (\Zend\Registry::isRegistered('Zend_Translator')) {
            $registry = \Zend\Registry::getInstance();
            unset($registry['Zend_Translator']);
        }
    }

    public function setUp()
    {
        $this->clearRegistry();
        AbstractValidator::setDefaultTranslator(null);
        StaticValidator::setPluginManager(null);
        $this->validator = new Alpha();
    }

    public function tearDown()
    {
        $this->clearRegistry();
        AbstractValidator::setDefaultTranslator(null);
        AbstractValidator::setMessageLength(-1);
    }

    /**
     * Ignores a raised PHP error when in effect, but throws a flag to indicate an error occurred
     *
     * @param  integer $errno
     * @param  string  $errstr
     * @param  string  $errfile
     * @param  integer $errline
     * @param  array   $errcontext
     * @return void
     */
    public function errorHandlerIgnore($errno, $errstr, $errfile, $errline, array $errcontext)
    {
        $this->errorOccurred = true;
    }

    public function testCanSetGlobalDefaultTranslator()
    {
        set_error_handler(array($this, 'errorHandlerIgnore'));
        $translator = new Translator\Translator('ArrayAdapter', array(), 'en');
        restore_error_handler();
        AbstractValidator::setDefaultTranslator($translator);
        $this->assertSame($translator->getAdapter(), AbstractValidator::getDefaultTranslator());
    }

    public function testGlobalDefaultTranslatorUsedWhenNoLocalTranslatorSet()
    {
        $this->testCanSetGlobalDefaultTranslator();
        $this->assertSame(AbstractValidator::getDefaultTranslator(), $this->validator->getTranslator());
    }

    public function testGlobalTranslatorFromRegistryUsedWhenNoLocalTranslatorSet()
    {
        set_error_handler(array($this, 'errorHandlerIgnore'));
        $translate = new Translator\Translator('ArrayAdapter', array());
        restore_error_handler();
        \Zend\Registry::set('Zend_Translator', $translate);
        $this->assertSame($translate->getAdapter(), $this->validator->getTranslator());
    }

    public function testLocalTranslatorPreferredOverGlobalTranslator()
    {
        $this->testCanSetGlobalDefaultTranslator();
        set_error_handler(array($this, 'errorHandlerIgnore'));
        $translator = new Translator\Translator('ArrayAdapter', array(), 'en');
        restore_error_handler();
        $this->validator->setTranslator($translator);
        $this->assertNotSame(AbstractValidator::getDefaultTranslator(), $this->validator->getTranslator());
    }

    public function testMaximumErrorMessageLength()
    {
        $this->assertEquals(-1, AbstractValidator::getMessageLength());
        AbstractValidator::setMessageLength(10);
        $this->assertEquals(10, AbstractValidator::getMessageLength());

        $translator = new Translator\Translator(
            'ArrayAdapter',
            array(Alpha::INVALID => 'This is the translated message for %value%'),
            'en'
        );
        $this->validator->setTranslator($translator);
        $this->assertFalse($this->validator->isValid(123));
        $messages = $this->validator->getMessages();
        $this->assertTrue(array_key_exists(Alpha::INVALID, $messages));
        $this->assertEquals('This is...', $messages[Alpha::INVALID]);
    }

    public function testSetGetMessageLengthLimitation()
    {
        AbstractValidator::setMessageLength(5);
        $this->assertEquals(5, AbstractValidator::getMessageLength());

        $valid = new Between(1, 10);
        $this->assertFalse($valid->isValid(24));
        $message = current($valid->getMessages());
        $this->assertTrue(strlen($message) <= 5);
    }

    public function testSetGetDefaultTranslator()
    {
        set_error_handler(array($this, 'errorHandlerIgnore'));
        $translator = new Translator\Translator('ArrayAdapter', array(), 'en');
        restore_error_handler();
        AbstractValidator::setDefaultTranslator($translator);
        $this->assertSame($translator->getAdapter(), AbstractValidator::getDefaultTranslator());
    }

    /* plugin loading */

    public function testLazyLoadsValidatorPluginManagerByDefault()
    {
        $plugins = StaticValidator::getPluginManager();
        $this->assertInstanceOf('Zend\Validator\ValidatorPluginManager', $plugins);
    }

    public function testCanSetCustomPluginManager()
    {
        $plugins = new ValidatorPluginManager();
        StaticValidator::setPluginManager($plugins);
        $this->assertSame($plugins, StaticValidator::getPluginManager());
    }

    public function testPassingNullWhenSettingPluginManagerResetsPluginManager()
    {
        $plugins = new ValidatorPluginManager();
        StaticValidator::setPluginManager($plugins);
        $this->assertSame($plugins, StaticValidator::getPluginManager());
        StaticValidator::setPluginManager(null);
        $this->assertNotSame($plugins, StaticValidator::getPluginManager());
    }

    public function testExecuteValidWithParameters()
    {
        $this->assertTrue(StaticValidator::execute(5, 'Between', array(1, 10)));
        $this->assertTrue(StaticValidator::execute(5, 'Between', array('min' => 1, 'max' => 10)));
    }
}