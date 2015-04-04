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

use Zend\ServiceManager\AbstractAliasedPluginManager;
use Zend\ServiceManager\Factory\InvokableFactory;
use Zend\ServiceManager\ServiceLocatorInterface;
use ZendTest\ServiceManager\Asset\InvokableObject;

class AbstractAliasedPluginManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AbstractAliasedPluginManager
     */
    private $pluginManager;

    public function setUp()
    {
        $config = [
            'factories' => [
                InvokableObject::class => InvokableFactory::class
            ],
            'aliases' => [
                'foo' => InvokableObject::class,
                'bar' => 'foo'
            ]
        ];

        $serviceLocator      = $this->getMock(ServiceLocatorInterface::class);
        $this->pluginManager = $this->getMockForAbstractClass(
            AbstractAliasedPluginManager::class,
            [$serviceLocator, $config]
        );
    }

    public function testCreateObjectWithAlias()
    {
        $object = $this->pluginManager->get('bar');
        $this->assertInstanceOf(InvokableObject::class, $object);
    }

    public function testCheckObjectExistanceWithAlias()
    {
        $this->assertTrue($this->pluginManager->has('bar'));
        $this->assertFalse($this->pluginManager->has('baz'));
    }
}