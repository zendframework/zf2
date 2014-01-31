<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Application;

use Zend\Framework\Application\View\ListenerInterface as ViewListenerInterace;
use Zend\Framework\Event\EventTrait as EventTrait;
use Zend\Framework\Event\ListenerInterface;
use Zend\Framework\Event\ResultTrait as Result;
use Zend\Framework\Route\ServiceTrait as Route;
use Zend\Framework\View\Model\ServiceTrait as ViewModel;
use Zend\View\Model\ModelInterface as ViewModelInterface;

class Event
    implements EventInterface
{
    /**
     *
     */
    use EventTrait,
        Result,
        Route,
        ViewModel;

    /**
     * @var string
     */
    protected $name = self::EVENT_APPLICATION;

    /**
     * @param ListenerInterface $listener
     * @param $options
     * @return mixed
     */
    public function trigger(ListenerInterface $listener, $options = null)
    {
        $response = $listener->trigger($this, $options);

        switch(true) {
            case $response instanceof ViewModelInterface:
                $this->setViewModel($response);
                break;
            case $listener instanceof ViewListenerInterace:
                $this->setResult($response);
                break;
        }

        return $response;
    }
}
