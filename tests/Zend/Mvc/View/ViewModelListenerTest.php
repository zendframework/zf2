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
    Zend\Mvc\MvcEvent,
    Zend\Mvc\View\ViewModelListener,
    Zend\View\Model\ViewModel;

/**
 * @category   Zend
 * @package    Zend_Mvc
 * @subpackage UnitTest
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class ViewModelListenerTest extends TestCase
{
    public function setUp()
    {
        $this->event    = new MvcEvent();
        $this->listener = new ViewModelListener();
    }

    public function testReplacesEventModelWithChildModelIfChildIsMarkedTerminal()
    {
        $childModel  = new ViewModel();
        $childModel->setTerminal(true);
        $event = new MvcEvent();
        $event->setResult($childModel);

        $this->listener->insertViewModel($event);
        $this->assertSame($childModel, $event->getViewModel());
    }

    public function testAddsViewModelAsChildOfEventViewModelWhenChildIsNotTerminal()
    {
        $childModel  = new ViewModel();
        $event       = new MvcEvent();
        $event->setResult($childModel);

        $this->listener->insertViewModel($event);
        $model = $event->getViewModel();
        $this->assertNotSame($childModel, $model);
        $this->assertTrue($model->hasChildren());
        $this->assertEquals(1, count($model));
        $child = false;
        foreach ($model as $child) {
            break;
        }
        $this->assertSame($childModel, $child);
    }
}
