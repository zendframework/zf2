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
 * @package    Zend_Mvc
 * @subpackage UnitTest
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Mvc\View;

use PHPUnit_Framework_TestCase as TestCase,
    Zend\Http\Request,
    Zend\Http\Response,
    Zend\Mvc\Application,
    Zend\Mvc\MvcEvent,
    Zend\Mvc\View\ExceptionStrategy,
    Zend\View\Model\ViewModel;

/**
 * @category   Zend
 * @package    Zend_Mvc
 * @subpackage UnitTest
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class ExceptionStrategyTest extends TestCase
{
    public function setUp()
    {
        $this->strategy = new ExceptionStrategy();
    }

    public function testDisplayExceptionsIsDisabledByDefault()
    {
        $this->assertFalse($this->strategy->displayExceptions());
    }

    public function testDisplayExceptionsFlagIsMutable()
    {
        $this->strategy->setDisplayExceptions(true);
        $this->assertTrue($this->strategy->displayExceptions());
    }

    public function testErrorTemplateHasASaneDefault()
    {
        $this->assertEquals('error', $this->strategy->getErrorTemplate());
    }

    public function testErrorTemplateIsMutable()
    {
        $this->strategy->setErrorTemplate('pages/error');
        $this->assertEquals('pages/error', $this->strategy->getErrorTemplate());
    }

    public function test404ApplicationErrorsResultInNoOperations()
    {
        $event = new MvcEvent();
        foreach (array(Application::ERROR_CONTROLLER_NOT_FOUND, Application::ERROR_CONTROLLER_INVALID) as $error) {
            $event->setError($error);
            $this->strategy->prepareExceptionViewModel($event);
            $response = $event->getResponse();
            if (null !== $response) {
                $this->assertNotEquals(500, $response->getStatusCode());
            }
            $model = $event->getResult();
            if (null !== $model) {
                $variables = $model->getVariables();
                $this->assertArrayNotHasKey('message', $variables);
                $this->assertArrayNotHasKey('exception', $variables);
                $this->assertArrayNotHasKey('display_exceptions', $variables);
                $this->assertNotEquals('error', $model->getTemplate());
            }
        }
    }

    public function testCatchesApplicationExceptions()
    {
        $exception = new \Exception;
        $event     = new MvcEvent();
        $event->setParam('exception', $exception)
              ->setError(Application::ERROR_EXCEPTION);
        $this->strategy->prepareExceptionViewModel($event);

        $response = $event->getResponse();
        $this->assertTrue($response->isServerError());

        $model = $event->getResult();
        $this->assertInstanceOf('Zend\View\Model\ViewModel', $model);
        $this->assertEquals($this->strategy->getErrorTemplate(), $model->getTemplate());

        $variables = $model->getVariables();
        $this->assertArrayHasKey('message', $variables);
        $this->assertContains('error occurred', $variables['message']);
        $this->assertArrayHasKey('exception', $variables);
        $this->assertSame($exception, $variables['exception']);
        $this->assertArrayHasKey('display_exceptions', $variables);
        $this->assertEquals($this->strategy->displayExceptions(), $variables['display_exceptions']);
    }

    public function testCatchesUnknownErrorTypes()
    {
        $exception = new \Exception;
        $event     = new MvcEvent();
        $event->setParam('exception', $exception)
              ->setError('custom_error');
        $this->strategy->prepareExceptionViewModel($event);

        $response = $event->getResponse();
        $this->assertTrue($response->isServerError());
    }

    public function testEmptyErrorInEventResultsInNoOperations()
    {
        $event = new MvcEvent();
        $this->strategy->prepareExceptionViewModel($event);
        $response = $event->getResponse();
        if (null !== $response) {
            $this->assertNotEquals(500, $response->getStatusCode());
        }
        $model = $event->getResult();
        if (null !== $model) {
            $variables = $model->getVariables();
            $this->assertArrayNotHasKey('message', $variables);
            $this->assertArrayNotHasKey('exception', $variables);
            $this->assertArrayNotHasKey('display_exceptions', $variables);
            $this->assertNotEquals('error', $model->getTemplate());
        }
    }
}
