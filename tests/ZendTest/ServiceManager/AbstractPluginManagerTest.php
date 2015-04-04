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
use Zend\ServiceManager\Exception\InvalidServiceException;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\ServiceManager\Factory\InvokableFactory;
use Zend\ServiceManager\ServiceLocatorInterface;
use ZendTest\ServiceManager\Asset\InvokableObject;
use ZendTest\ServiceManager\Asset\SimplePluginManager;

class AbstractPluginManagerTest extends \PHPUnit_Framework_TestCase
{
    public function testInjectCreationContextInFactories()
    {
        $invokableFactory = $this->getMock(FactoryInterface::class);

        $config = [
            'factories' => [
                InvokableObject::class => $invokableFactory
            ]
        ];

        $serviceLocator = $this->getMock(ServiceLocatorInterface::class);
        $pluginManager  = new SimplePluginManager($serviceLocator, $config);

        $invokableFactory->expects($this->once())
                         ->method('__invoke')
                         ->with($serviceLocator, InvokableObject::class)
                         ->will($this->returnValue(new InvokableObject()));

        $object = $pluginManager->get(InvokableObject::class);

        $this->assertInstanceOf(InvokableObject::class, $object);
    }

    public function testValidateInstance()
    {
        $config = [
            'factories' => [
                InvokableObject::class => new InvokableFactory(),
                stdClass::class          => new InvokableFactory()
            ]
        ];

        $serviceLocator = $this->getMock(ServiceLocatorInterface::class);
        $pluginManager  = new SimplePluginManager($serviceLocator, $config);

        // Assert no exception is triggered because the plugin manager validate ObjectWithOptions
        $pluginManager->get(InvokableObject::class);

        // Assert it throws an exception for anything else
        $this->setExpectedException(InvalidServiceException::class);
        $pluginManager->get(stdClass::class);
    }
}