<?php
/**
 * @author Stefano Torresi (http://stefanotorresi.it)
 * @license See the file LICENSE.txt for copying permission.
 * ************************************************
 */

namespace ZendTest\Mail\Transport;

use PHPUnit_Framework_TestCase;
use Zend\Mail\Transport\Factory;
use Zend\Stdlib\ArrayObject;

class FactoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider invalidSpecTypeProvider
     * @expectedException \Zend\Mail\Transport\Exception\InvalidArgumentException
     * @param $spec
     */
    public function testInvalidSpecThrowsInvalidArgumentException($spec)
    {
        Factory::create($spec);
    }

    public function invalidSpecTypeProvider()
    {
        return [
            ['spec'],
            [new \stdClass()],
        ];
    }

    /**
     *
     */
    public function testDefaultTypeIsSendmail()
    {
        $transport = Factory::create();

        $this->assertInstanceOf('Zend\Mail\Transport\Sendmail', $transport);
    }

    /**
     * @dataProvider typeProvider
     * @param $type
     */
    public function testCanCreateClassUsingTypeKey($type)
    {
        set_error_handler(function ($code, $message) {
            // skip deprecation notices
            return;
        }, E_USER_DEPRECATED);
        $transport = Factory::create([
            'type' => $type,
        ]);
        restore_error_handler();

        $this->assertInstanceOf($type, $transport);
    }

    public function typeProvider()
    {
        $types = [
            ['Zend\Mail\Transport\File'],
            ['Zend\Mail\Transport\InMemory'],
            ['Zend\Mail\Transport\Sendmail'],
            ['Zend\Mail\Transport\Smtp'],
        ];

        if (version_compare(PHP_VERSION, '7.0', '<')) {
            $types[] = ['Zend\Mail\Transport\Null'];
        }

        return $types;
    }

    /**
     * @dataProvider typeAliasProvider
     * @param $type
     * @param $expectedClass
     */
    public function testCanCreateClassFromTypeAlias($type, $expectedClass)
    {
        $transport = Factory::create([
            'type' => $type,
        ]);

        $this->assertInstanceOf($expectedClass, $transport);
    }

    public function typeAliasProvider()
    {
        return [
            ['file', 'Zend\Mail\Transport\File'],
            ['null', 'Zend\Mail\Transport\InMemory'],
            ['memory', 'Zend\Mail\Transport\InMemory'],
            ['inmemory', 'Zend\Mail\Transport\InMemory'],
            ['InMemory', 'Zend\Mail\Transport\InMemory'],
            ['sendmail', 'Zend\Mail\Transport\Sendmail'],
            ['smtp', 'Zend\Mail\Transport\Smtp'],
            ['File', 'Zend\Mail\Transport\File'],
            ['Null', 'Zend\Mail\Transport\InMemory'],
            ['NULL', 'Zend\Mail\Transport\InMemory'],
            ['Sendmail', 'Zend\Mail\Transport\Sendmail'],
            ['SendMail', 'Zend\Mail\Transport\Sendmail'],
            ['Smtp', 'Zend\Mail\Transport\Smtp'],
            ['SMTP', 'Zend\Mail\Transport\Smtp'],
        ];
    }

    /**
     *
     */
    public function testCanUseTraversableAsSpec()
    {
        $spec = new ArrayObject([
            'type' => 'null'
        ]);

        $transport = Factory::create($spec);

        $this->assertInstanceOf('Zend\Mail\Transport\InMemory', $transport);
    }

    /**
     * @dataProvider invalidClassProvider
     * @expectedException \Zend\Mail\Transport\Exception\DomainException
     * @param $class
     */
    public function testInvalidClassThrowsDomainException($class)
    {
        Factory::create([
            'type' => $class
        ]);
    }

    public function invalidClassProvider()
    {
        return [
            ['stdClass'],
            ['non-existent-class'],
        ];
    }

    /**
     *
     */
    public function testCanCreateSmtpTransportWithOptions()
    {
        $transport = Factory::create([
            'type' => 'smtp',
            'options' => [
                'host' => 'somehost',
            ]
        ]);

        $this->assertEquals($transport->getOptions()->getHost(), 'somehost');
    }

    /**
     *
     */
    public function testCanCreateFileTransportWithOptions()
    {
        $transport = Factory::create([
            'type' => 'file',
            'options' => [
                'path' => __DIR__,
            ]
        ]);

        $this->assertEquals($transport->getOptions()->getPath(), __DIR__);
    }
}
