<?php
/*
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license.
 */

namespace ZendTest\ServiceManager;

use stdClass;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\Factory\InvokableFactory;
use Zend\ServiceManager\ServiceManager;
use ZendTest\ServiceManager\Asset\InvokableObject;
use ZendTest\ServiceManager\Asset\SimpleAbstractFactory;

class ServiceManagerTest extends \PHPUnit_Framework_TestCase
{
    public function testIsSharedByDefault()
    {
        $serviceManager = new ServiceManager([
            'factories' => [
                stdClass::class => InvokableFactory::class
            ]
        ]);

        $object1 = $serviceManager->get(stdClass::class);
        $object2 = $serviceManager->get(stdClass::class);

        $this->assertSame($object1, $object2);
    }

    public function testCanDisableSharedByDefault()
    {
        $serviceManager = new ServiceManager([
            'factories' => [
                stdClass::class => InvokableFactory::class
            ],
            'shared_by_default' => false
        ]);

        $object1 = $serviceManager->get(stdClass::class);
        $object2 = $serviceManager->get(stdClass::class);

        $this->assertNotSame($object1, $object2);
    }

    public function testCanDisableSharedForSingleService()
    {
        $serviceManager = new ServiceManager([
            'factories' => [
                stdClass::class => InvokableFactory::class
            ],
            'shared' => [
                stdClass::class => false
            ]
        ]);

        $object1 = $serviceManager->get(stdClass::class);
        $object2 = $serviceManager->get(stdClass::class);

        $this->assertNotSame($object1, $object2);
    }

    public function testCanEnableSharedForSingleService()
    {
        $serviceManager = new ServiceManager([
            'factories' => [
                stdClass::class => InvokableFactory::class
            ],
            'shared_by_default' => false,
            'shared'            => [
                stdClass::class => true
            ]
        ]);

        $object1 = $serviceManager->get(stdClass::class);
        $object2 = $serviceManager->get(stdClass::class);

        $this->assertSame($object1, $object2);
    }

    public function testCanCreateObjectWithInvokableFactory()
    {
        $serviceManager = new ServiceManager([
            'factories' => [
                InvokableObject::class => InvokableFactory::class
            ]
        ]);

        $object = $serviceManager->get(InvokableObject::class, ['foo' => 'bar']);

        $this->assertInstanceOf(InvokableObject::class, $object);
        $this->assertEquals(['foo' => 'bar'], $object->options);
    }

    public function testCanCreateObjectWithClosureFactory()
    {
        $serviceManager = new ServiceManager([
            'factories' => [
                stdClass::class => function(ServiceLocatorInterface $serviceLocator, $className) {
                    $this->assertEquals(stdClass::class, $className);
                    return new stdClass();
                }
            ]
        ]);

        $object = $serviceManager->get(stdClass::class);
        $this->assertInstanceOf(stdClass::class, $object);
    }
}