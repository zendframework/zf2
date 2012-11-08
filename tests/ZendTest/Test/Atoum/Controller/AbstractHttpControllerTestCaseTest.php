<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Test
 */
namespace ZendTest\Test\Atoum\Controller;

use Zend\Mvc\Application;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Router\Http\RouteMatch;
use Zend\Stdlib\Parameters;
use Zend\Stdlib\RequestInterface;
use Zend\Stdlib\ResponseInterface;
use Zend\Test\Atoum\Controller\AbstractHttpControllerTestCase;
use Zend\View\Model\ViewModel;

/**
 * @category   Zend
 * @package    Zend_Test
 * @subpackage UnitTests
 * @group      Zend_Test
 */
class AbstractHttpControllerTestCaseTest extends AbstractHttpControllerTestCase
{
    public function beforeTestMethod($method)
    {
        $this->setApplicationConfig(
            include __DIR__ . '/../../_files/application.config.php'
        );
        parent::beforeTestMethod($method);
    }

    public function testUseOfRouter()
    {
       $this->boolean($this->getUseConsoleRequest())->isEqualTo(false);
    }

    public function testApplicationClass()
    {
        $this->object($this->getApplication())
                ->isInstanceOf('\Zend\Mvc\Application');
    }
    
    public function testApplicationServiceLocatorClass()
    {
        $this->object($this->getApplicationServiceLocator())
                ->isInstanceOf('Zend\ServiceManager\ServiceManager');
    }
    
    public function testAssertApplicationRequest()
    {
        $this->object($this->getRequest())
                ->isInstanceOf('Zend\Stdlib\RequestInterface');
    }

    public function testAssertApplicationResponse()
    {
        $this->object($this->getResponse())
                ->isInstanceOf('Zend\Stdlib\ResponseInterface');
    }
    
    public function testAssertResponseStatusCode()
    {
        $this->dispatch('/tests');
        $this->assertResponseStatusCode(200);
        
        $self = $this;
        $this->exception(function() use ($self) {$self->assertResponseStatusCode(302); })
                ->isInstanceOf('Zend\Test\Atoum\Exception\ExpectationFailedException');
    }
    
    public function testAssertNotResponseStatusCode()
    {
        $this->dispatch('/tests');
        $this->assertNotResponseStatusCode(302);
        
        $self = $this;
        $this->exception(function() use ($self) {$self->assertNotResponseStatusCode(200); })
                ->isInstanceOf('Zend\Test\Atoum\Exception\ExpectationFailedException');
    }

    public function testAssertModuleName()
    {
        $this->dispatch('/tests');

        // tests with case insensitive
        $this->assertModule('baz');
        $this->assertModule('Baz');
        $this->assertModule('BAz');

        $self = $this;
        $this->exception(function() use ($self) {$self->assertModule('Application'); })
                ->isInstanceOf('Zend\Test\Atoum\Exception\ExpectationFailedException');
    }

    public function testAssertNotModuleName()
    {
        $this->dispatch('/tests');
        $this->assertNotModule('Application');

        $self = $this;
        $this->exception(function() use ($self) {$self->assertNotModule('baz'); })
                ->isInstanceOf('Zend\Test\Atoum\Exception\ExpectationFailedException');
    }
    
    public function testAssertControllerClass()
    {
        $this->dispatch('/tests');

        // tests with case insensitive
        $this->assertControllerClass('IndexController');
        $this->assertControllerClass('Indexcontroller');
        $this->assertControllerClass('indexcontroller');

        $self = $this;
        $this->exception(function() use ($self) {$self->assertControllerClass('Index'); })
                ->isInstanceOf('Zend\Test\Atoum\Exception\ExpectationFailedException');
    }

    public function testAssertNotControllerClass()
    {
        $this->dispatch('/tests');
        $this->assertNotControllerClass('Index');

        $self = $this;
        $this->exception(function() use ($self) {$self->assertNotControllerClass('IndexController'); })
                ->isInstanceOf('Zend\Test\Atoum\Exception\ExpectationFailedException');
    }

    public function testAssertControllerName()
    {
        $this->dispatch('/tests');

        // tests with case insensitive
        $this->assertControllerName('baz_index');
        $this->assertControllerName('Baz_index');
        $this->assertControllerName('BAz_index');

        $self = $this;
        $this->exception(function() use ($self) {$self->assertControllerName('baz'); })
                ->isInstanceOf('Zend\Test\Atoum\Exception\ExpectationFailedException');
    }

    public function testAssertNotControllerName()
    {
        $this->dispatch('/tests');
        $this->assertNotControllerName('baz');

        $self = $this;
        $this->exception(function() use ($self) {$self->assertNotControllerName('baz_index'); })
                ->isInstanceOf('Zend\Test\Atoum\Exception\ExpectationFailedException');
    }

    public function testAssertActionName()
    {
        $this->dispatch('/tests');

        // tests with case insensitive
        $this->assertActionName('unittests');
        $this->assertActionName('unitTests');
        $this->assertActionName('UnitTests');

        $self = $this;
        $this->exception(function() use ($self) {$self->assertActionName('unit'); })
                ->isInstanceOf('Zend\Test\Atoum\Exception\ExpectationFailedException');
    }

    public function testAssertNotActionName()
    {
        $this->dispatch('/tests');
        $this->assertNotActionName('unit');

        $self = $this;
        $this->exception(function() use ($self) {$self->assertNotActionName('unittests'); })
                ->isInstanceOf('Zend\Test\Atoum\Exception\ExpectationFailedException');
    }

    public function testAssertMatchedRouteName()
    {
        $this->dispatch('/tests');

        // tests with case insensitive
        $this->assertMatchedRouteName('myroute');
        $this->assertMatchedRouteName('myRoute');
        $this->assertMatchedRouteName('MyRoute');
        
        $self = $this;
        $this->exception(function() use ($self) {$self->assertMatchedRouteName('route'); })
                ->isInstanceOf('Zend\Test\Atoum\Exception\ExpectationFailedException');
    }

    public function testAssertNotMatchedRouteName()
    {
        $this->dispatch('/tests');
        $this->assertNotMatchedRouteName('route');
        
        $self = $this;
        $this->exception(function() use ($self) {$self->assertNotMatchedRouteName('myroute'); })
                ->isInstanceOf('Zend\Test\Atoum\Exception\ExpectationFailedException');
    }

    public function testAssertQuery()
    {
        $this->dispatch('/tests');
        $this->assertQuery('form#myform');

        $self = $this;
        $this->exception(function() use ($self) {$self->assertQuery('form#id'); })
                ->isInstanceOf('Zend\Test\Atoum\Exception\ExpectationFailedException');
    }

    public function testAssertNotQuery()
    {
        $this->dispatch('/tests');
        $this->assertNotQuery('form#id');
        
        $self = $this;
        $this->exception(function() use ($self) {$self->assertNotQuery('form#myform'); })
                ->isInstanceOf('Zend\Test\Atoum\Exception\ExpectationFailedException');
    }

    public function testAssertQueryCount()
    {
        $this->dispatch('/tests');
        $this->assertQueryCount('div.top', 3);
        
        $self = $this;
        $this->exception(function() use ($self) {$self->assertQueryCount('div.top', 2); })
                ->isInstanceOf('Zend\Test\Atoum\Exception\ExpectationFailedException');
    }

    public function testAssertNotQueryCount()
    {
        $this->dispatch('/tests');
        $this->assertNotQueryCount('div.top', 1);
        $this->assertNotQueryCount('div.top', 2);
        
        $self = $this;
        $this->exception(function() use ($self) {$self->assertNotQueryCount('div.top', 3); })
                ->isInstanceOf('Zend\Test\Atoum\Exception\ExpectationFailedException');
    }

    public function testAssertQueryCountMin()
    {
        $this->dispatch('/tests');
        $this->assertQueryCountMin('div.top', 1);
        $this->assertQueryCountMin('div.top', 2);
        $this->assertQueryCountMin('div.top', 3);
        
        $self = $this;
        $this->exception(function() use ($self) {$self->assertQueryCountMin('div.top', 4); })
                ->isInstanceOf('Zend\Test\Atoum\Exception\ExpectationFailedException');
    }

    public function testAssertQueryCountMax()
    {
        $this->dispatch('/tests');
        $this->assertQueryCountMax('div.top', 5);
        $this->assertQueryCountMax('div.top', 4);
        $this->assertQueryCountMax('div.top', 3);
        
        $self = $this;
        $this->exception(function() use ($self) {$self->assertQueryCountMax('div.top', 2); })
                ->isInstanceOf('Zend\Test\Atoum\Exception\ExpectationFailedException');
    }

    public function testAssertQueryWithDynamicQueryParams()
    {
        $this->getRequest()
            ->setMethod('GET')
            ->setQuery(new Parameters(array('num_get' => 5)));
        $this->dispatch('/tests');
        $this->assertQueryCount('div.get', 5);
        $this->assertQueryCount('div.post', 0);
    }

    public function testAssertQueryWithDynamicPostParams()
    {
        $this->getRequest()
            ->setMethod('POST')
            ->setPost(new Parameters(array('num_post' => 5)));
        $this->dispatch('/tests');
        $this->assertQueryCount('div.post', 5);
        $this->assertQueryCount('div.get', 0);
    }

    public function testAssertUriWithHostname()
    {
        $this->dispatch('http://my.domain.tld:443');
        $routeMatch = $this->getApplication()->getMvcEvent()->getRouteMatch();
        $this->string($routeMatch->getParam('subdomain'))->isEqualTo('my');
        $this->integer($this->getRequest()->getUri()->getPort())->isEqualTo(443);
    }
    
    /**
     * Sample tests on MvcEvent
     */
    public function testAssertApplicationMvcEvent()
    {
        $this->dispatch('/tests');

        // get and assert mvc event
        $mvcEvent = $this->getApplication()->getMvcEvent();
        $this->object($mvcEvent)->isInstanceOf('Zend\Mvc\MvcEvent');
        $this->object($mvcEvent->getApplication())->isEqualTo($this->getApplication());

        // get and assert view controller
        $viewModel = $mvcEvent->getResult();
        $this->object($viewModel)->isInstanceOf('Zend\View\Model\ViewModel');
        $this->string($viewModel->getTemplate())->isEqualTo('baz/index/unittests');

        // get and assert view manager layout
        $layout = $mvcEvent->getViewModel();
        $this->object($layout)->isInstanceOf('Zend\View\Model\ViewModel');
        $this->string($layout->getTemplate())->isEqualTo('layout/layout');

        // children layout must be the controller view
        $this->object($viewModel)->isEqualTo(current($layout->getChildren()));
    }

    /**
     * Sample tests on Application events
     */
    public function testAssertApplicationEvents()
    {
        $this->url('/tests');

        $result = $this->triggerApplicationEvent(MvcEvent::EVENT_ROUTE);
        $routeMatch = $result->last();
        $this->boolean($result->stopped())->isEqualTo(false);
        $this->string($this->getApplication()->getMvcEvent()->getError())->isEqualTo('');
        $this->object($routeMatch)->isInstanceOf('Zend\Mvc\Router\Http\RouteMatch');
        $this->string($routeMatch->getParam('controller'))->isEqualTo('baz_index');

        $result = $this->triggerApplicationEvent(MvcEvent::EVENT_DISPATCH);
        $viewModel = $this->getApplication()->getMvcEvent()->getResult();
        $this->object($viewModel)->isInstanceOf('Zend\View\Model\ViewModel');
        $this->string($viewModel->getTemplate())->isEqualTo('baz/index/unittests');
    }

    /**
     * Sample tests on Application errors events
     */
    public function testAssertApplicationErrorsEvents()
    {
        $this->url('/bad-url');
        $result = $this->triggerApplicationEvent(MvcEvent::EVENT_ROUTE);
        $this->boolean($result->stopped())->isEqualTo(true);
        $this->string($this->getApplication()->getMvcEvent()->getError())->isEqualTo(Application::ERROR_ROUTER_NO_MATCH);
    }
}
