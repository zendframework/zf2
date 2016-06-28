<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Crypt\Password\Options\PasswordHandlerAggregateOptions;

use Zend\Crypt\Password\Options\PasswordHandlerAggregateOptions;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * @covers Zend\Crypt\Password\Options\PasswordHandlerAggregateOptions
 */
class PasswordHandlerAggregateOptionsTest extends TestCase
{
    public function testDefaults()
    {
        $options = new PasswordHandlerAggregateOptions();
        $this->assertEquals(array('Bcrypt', 'SimpleSha1', 'SimpleMd5'), $options->getHashingMethods());
        $this->assertEquals('bcrypt', $options->getDefaultHashingMethod());
        $this->assertTrue($options->getMigrateToDefaultHashingMethod(), 'migrate_to_default_hashing_method must default to true');
    }

    public function testSetValues()
    {
        $options = new PasswordHandlerAggregateOptions(array(
            'hashing_methods'                   => array('SimpleSha1'),
            'default_hashing_method'            => 'simplesha1',
            'migrate_to_default_hashing_method' => false,
        ));

        $this->assertEquals(array('SimpleSha1'), $options->getHashingMethods());
        $this->assertEquals('simplesha1', $options->getDefaultHashingMethod());
        $this->assertFalse($options->getMigrateToDefaultHashingMethod(), 'migrate_to_default_hashing_method must be false');
    }
}
