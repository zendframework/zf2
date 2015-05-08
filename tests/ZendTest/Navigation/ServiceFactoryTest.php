<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Navigation;

use Zend\Config;
use Zend\Mvc\Router\RouteMatch;
use Zend\Mvc\Service\ServiceManagerConfig;
use Zend\Navigation;
use Zend\Navigation\Page\Mvc as MvcPage;
use Zend\Navigation\Service\ConstructedNavigationFactory;
use Zend\Navigation\Service\NavigationAbstractServiceFactory;
use Zend\ServiceManager\ServiceManager;

/**
 * Tests the class Zend\Navigation\MvcNavigationFactory
 *
 * @group      Zend_Navigation
 */
class ServiceFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Zend\ServiceManager\ServiceManager
     */
    protected $serviceManager;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        $config = [
            'modules'                 => [],
            'module_listener_options' => [
                'config_cache_enabled' => false,
                'cache_dir'            => 'data/cache',
                'module_paths'         => [],
                'extra_config'         => [
                    'service_manager' => [
                        'factories' => [
                            'Config' => function () {
                                return [
                                    'navigation' => [
                                        'file'    => __DIR__ . '/_files/navigation.xml',
                                        'default' => [
                                            [
                                                'label' => 'Page 1',
                                                'uri'   => 'page1.html'
                                            ],
                                            [
                                                'label' => 'MVC Page',
                                                'route' => 'foo',
                                                'pages' => [
                                                    [
                                                        'label' => 'Sub MVC Page',
                                                        'route' => 'foo'
                                                    ]
                                                ]
                                            ],
                                            [
                                                'label' => 'Page 3',
                                                'uri'   => 'page3.html'
                                            ]
                                        ]
                                    ]
                                ];
                            }
                        ]
                    ],
                ]
            ],
        ];

        $sm = $this->serviceManager = new ServiceManager(new ServiceManagerConfig);
        $sm->setService('ApplicationConfig', $config);
        $sm->get('ModuleManager')->loadModules();
        $sm->get('Application')->bootstrap();

        $app = $this->serviceManager->get('Application');
        $app->getMvcEvent()->setRouteMatch(new RouteMatch([
            'controller' => 'post',
            'action'     => 'view',
            'id'         => '1337',
        ]));
    }

    /**
     * Tear down the environment after running a test
     */
    protected function tearDown()
    {
    }

    /**
     * @covers \Zend\Navigation\Service\AbstractNavigationFactory
     */
    public function testDefaultFactoryAcceptsFileString()
    {
        $this->serviceManager->setFactory('Navigation', 'ZendTest\Navigation\TestAsset\FileNavigationFactory');
        $container = $this->serviceManager->get('Navigation');
    }

    /**
     * @covers \Zend\Navigation\Service\DefaultNavigationFactory
     */
    public function testMvcPagesGetInjectedWithComponents()
    {
        $this->serviceManager->setFactory('Navigation', 'Zend\Navigation\Service\DefaultNavigationFactory');
        $container = $this->serviceManager->get('Navigation');

        $recursive = function ($that, $pages) use (&$recursive) {
            foreach ($pages as $page) {
                if ($page instanceof MvcPage) {
                    $that->assertInstanceOf('Zend\Mvc\Router\RouteStackInterface', $page->getRouter());
                    $that->assertInstanceOf('Zend\Mvc\Router\RouteMatch', $page->getRouteMatch());
                }

                $recursive($that, $page->getPages());
            }
        };
        $recursive($this, $container->getPages());
    }

    /**
     * @covers \Zend\Navigation\Service\ConstructedNavigationFactory
     */
    public function testConstructedNavigationFactoryInjectRouterAndMatcher()
    {
        $builder = $this->getMockBuilder('\Zend\Navigation\Service\ConstructedNavigationFactory');
        $builder->setConstructorArgs([__DIR__ . '/_files/navigation_mvc.xml'])
                ->setMethods(['injectComponents']);

        $factory = $builder->getMock();

        $factory->expects($this->once())
                ->method('injectComponents')
                ->with($this->isType("array"),
                       $this->isInstanceOf("Zend\Mvc\Router\RouteMatch"),
                       $this->isInstanceOf("Zend\Mvc\Router\RouteStackInterface"));

        $this->serviceManager->setFactory('Navigation', function ($serviceLocator) use ($factory) {
              return $factory->createService($serviceLocator);
        });

        $container = $this->serviceManager->get('Navigation');
    }

    /**
     * @covers \Zend\Navigation\Service\ConstructedNavigationFactory
     */
    public function testMvcPagesGetInjectedWithComponentsInConstructedNavigationFactory()
    {
        $this->serviceManager->setFactory('Navigation', function ($serviceLocator) {
              $argument = __DIR__ . '/_files/navigation_mvc.xml';
              $factory = new \Zend\Navigation\Service\ConstructedNavigationFactory($argument);
              return $factory->createService($serviceLocator);
        });

        $container = $this->serviceManager->get('Navigation');
        $recursive = function ($that, $pages) use (&$recursive) {
            foreach ($pages as $page) {
                if ($page instanceof MvcPage) {
                    $that->assertInstanceOf('Zend\Mvc\Router\RouteStackInterface', $page->getRouter());
                    $that->assertInstanceOf('Zend\Mvc\Router\RouteMatch', $page->getRouteMatch());
                }

                $recursive($that, $page->getPages());
            }
        };
        $recursive($this, $container->getPages());
    }

    /**
     * @covers \Zend\Navigation\Service\DefaultNavigationFactory
     */
    public function testDefaultFactory()
    {
        $this->serviceManager->setFactory('Navigation', 'Zend\Navigation\Service\DefaultNavigationFactory');

        $container = $this->serviceManager->get('Navigation');
        $this->assertEquals(3, $container->count());
    }

    /**
     * @covers \Zend\Navigation\Service\ConstructedNavigationFactory
     */
    public function testConstructedFromArray()
    {
        $argument = [
            [
                'label' => 'Page 1',
                'uri'   => 'page1.html'
            ],
            [
                'label' => 'Page 2',
                'uri'   => 'page2.html'
            ],
            [
                'label' => 'Page 3',
                'uri'   => 'page3.html'
            ]
        ];

        $factory = new ConstructedNavigationFactory($argument);
        $this->serviceManager->setFactory('Navigation', $factory);

        $container = $this->serviceManager->get('Navigation');
        $this->assertEquals(3, $container->count());
    }

    /**
     * @covers \Zend\Navigation\Service\ConstructedNavigationFactory
     */
    public function testConstructedFromFileString()
    {
        $argument = __DIR__ . '/_files/navigation.xml';
        $factory  = new ConstructedNavigationFactory($argument);
        $this->serviceManager->setFactory('Navigation', $factory);

        $container = $this->serviceManager->get('Navigation');
        $this->assertEquals(3, $container->count());
    }

    /**
     * @covers \Zend\Navigation\Service\ConstructedNavigationFactory
     */
    public function testConstructedFromConfig()
    {
        $argument = new Config\Config([
            [
                'label' => 'Page 1',
                'uri'   => 'page1.html'
            ],
            [
                'label' => 'Page 2',
                'uri'   => 'page2.html'
            ],
            [
                'label' => 'Page 3',
                'uri'   => 'page3.html'
            ]
        ]);

        $factory = new ConstructedNavigationFactory($argument);
        $this->serviceManager->setFactory('Navigation', $factory);

        $container = $this->serviceManager->get('Navigation');
        $this->assertEquals(3, $container->count());
    }

    /**
     * @covers \Zend\Navigation\Service\NavigationAbstractServiceFactory
     */
    public function testNavigationAbstractServiceFactory()
    {
        $factory = new NavigationAbstractServiceFactory();

        $this->assertTrue(
            $factory->canCreateServiceWithName($this->serviceManager, 'zendnavigationfile', 'Zend\Navigation\File')
        );
        $this->assertFalse(
            $factory->canCreateServiceWithName($this->serviceManager, 'zendnavigationunknown', 'Zend\Navigation\Unknown')
        );

        $container = $factory->createServiceWithName(
            $this->serviceManager,
            'zendnavigationfile',
            'Zend\Navigation\File'
        );

        $this->assertInstanceOf('Zend\Navigation\Navigation', $container);
        $this->assertEquals(3, $container->count());
    }
}
