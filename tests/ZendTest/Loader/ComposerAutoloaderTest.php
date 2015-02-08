<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Loader;

use Zend\Loader\ComposerAutoloader;
use Zend\Loader\Exception\InvalidArgumentException;
use Zend\Loader\Exception\RuntimeException;
use Composer\Autoload\ClassLoader;

/**
 * @author Nikola Posa <posa.nikola@gmail.com>
 */
class ComposerAutoloaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException RuntimeException
     */
    public function testComposerClassLoaderMustBeSuppliedThroughConstructor()
    {
        new ComposerAutoloader();
    }

    public function testSettingComposerClassLoaderThroughConstructor()
    {
        $classLoader = new ClassLoader();
        $autoloader = new ComposerAutoloader($classLoader);
        $this->assertAttributeEquals($classLoader, 'composerAutoloader', $autoloader);
    }

    public function testSettingComposerClassLoaderThroughConstructorAsArrayOption()
    {
        $classLoader = new ClassLoader();
        $autoloader = new ComposerAutoloader(array(
            'composer_autoloader' => $classLoader
        ));
        $this->assertAttributeEquals($classLoader, 'composerAutoloader', $autoloader);
    }

    public function testPassingNonTraversableOptionsToSetOptionsRaisesException()
    {
        $autoloader = new ComposerAutoloader(new ClassLoader());

        $obj  = new \stdClass();
        foreach (array(true, 'foo', $obj) as $arg) {
            try {
                $autoloader->setOptions(true);
                $this->fail('Setting options with invalid type should fail');
            } catch (InvalidArgumentException $e) {
                $this->assertContains('array or Traversable', $e->getMessage());
            }
        }
    }

    public function testPassingArrayOptionsPopulatesProperties()
    {
        $options = array(
            'namespaces' => array(
                'Zend\\' => array(dirname(__DIR__) . DIRECTORY_SEPARATOR),
            ),
            'psr4'   => array(
                'Foo\\' => array('/path/to/module/Foo/src'),
            ),
            'classmap' => array(
                'Foo/Service/Bar' => '/path/to/module/Foo/src/Service/Bar.php',
            )
        );

        $autoloader = new TestAsset\ComposerAutoloader(new ClassLoader());
        $autoloader->setOptions($options);

        $this->assertEquals($options['namespaces'], $autoloader->getNamespaces());
        $this->assertEquals($options['psr4'], $autoloader->getNamespacesPsr4());
        $this->assertEquals($options['classmap'], $autoloader->getClassMap());
    }

    public function testPassingTraversableOptionsPopulatesProperties()
    {
        $namespaces = array(
            'Zend\\' => array(dirname(__DIR__) . DIRECTORY_SEPARATOR),
        );
        $namespacesPsr4 = array(
            'Foo\\' => array('/path/to/module/Foo/src'),
        );
        $classmap = array(
            'Foo/Service/Bar' => '/path/to/module/Foo/src/Service/Bar.php',
        );
        $options = new \ArrayObject(array(
            'namespaces' => $namespaces,
            'psr4'   => $namespacesPsr4,
            'classmap' => $classmap,
        ));

        $autoloader = new TestAsset\ComposerAutoloader(new ClassLoader());
        $autoloader->setOptions($options);

        $this->assertEquals($options['namespaces'], $autoloader->getNamespaces());
        $this->assertEquals($options['psr4'], $autoloader->getNamespacesPsr4());
        $this->assertEquals((array) $options['classmap'], $autoloader->getClassMap());
    }
}
