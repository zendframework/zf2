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
 * @package    Zend_EventManager
 * @subpackage UnitTests
 */

namespace ZendTest\EventManager\TestAsset;

use Zend\EventManager\EventManagerInterface,
    Zend\EventManager\EventManager;

/**
 * @category   Zend
 * @package    Zend_EventManager
 * @subpackage UnitTests
 * @group      Zend_EventManager
 */
class ClassWithEvents
{
    protected $events;

    public function getEventManager(EventManagerInterface $events = null)
    {
        if (null !== $events) {
            $this->events = $events;
        }
        if (null === $this->events) {
            $this->events = new EventManager(__CLASS__);
        }
        return $this->events;
    }

    public function foo()
    {
        $this->getEventManager()->trigger(__FUNCTION__, $this, array());
    }
}
