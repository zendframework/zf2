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
 * @package    Zend_Code
 * @subpackage Annotation
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Code\Annotation;

use Zend\EventManager\Event;
use Zend\EventManager\EventManager;
use Zend\EventManager\EventManagerAwareInterface;
use Zend\EventManager\EventManagerInterface;

/**
 * Pluggable annotation manager
 *
 * Simply composes an EventManager. When createAnnotation() is called, it fires
 * off an event of the same name, passing it the resolved annotation class, the
 * annotation content, and the raw annotation string; the first listener to 
 * return an object will halt execution of the event, and that object will be
 * returned as the annotation.
 *
 * @category   Zend
 * @package    Zend_Code
 * @subpackage Annotation
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class AnnotationManager implements EventManagerAwareInterface
{
    /**
     * @var EventManagerInterface
     */
    protected $events;

    /**
     * Set the event manager instance
     * 
     * @param  EventManagerInterface $events 
     * @return void
     */
    public function setEventManager(EventManagerInterface $events)
    {
        $events->setIdentifiers(array(
            __CLASS__,
            get_class($this),
        ));
        $this->events = $events;
        return $this;
    }

    /**
     * Retrieve event manager
     *
     * Lazy loads an instance if none registered.
     * 
     * @return EventManagerInterface
     */
    public function events()
    {
        if (null === $this->events) {
            $this->setEventManager(new EventManager());
        }
        return $this->events;
    }

    /**
     * Create Annotation
     *
     * @param  array $annotationData
     * @return false|\stdClass
     */
    public function createAnnotation(array $annotationData)
    {
        $event = new Event();
        $event->setName(__FUNCTION__);
        $event->setTarget($this);
        $event->setParams(array(
            'class'   => $annotationData[0],
            'content' => $annotationData[1],
            'raw'     => $annotationData[2],
        ));

        $events  = $this->events();
        $results = $events->trigger($event, function ($r) {
            return (is_object($r));
        });
        $annotation = $results->last();
        return (is_object($annotation) ? $annotation : false);
    }
}
