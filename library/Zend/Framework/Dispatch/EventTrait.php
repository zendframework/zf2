<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Dispatch;

use Zend\Framework\Service\ServicesTrait as Services;
use Zend\Framework\EventManager\EventTrait as Event;
use Zend\Framework\EventManager\Manager\ServicesTrait as EventManager;
use Zend\Framework\Controller\ServicesTrait as Controller;
use Zend\Framework\Route\ServicesTrait as Route;
use Zend\Framework\Response\ServicesTrait as Response;
use Zend\Framework\View\ServicesTrait as View;

trait EventTrait
{
    /**
     *
     */
    use Controller, Event, EventManager, Services, Response, Route, View;

    /**
     * @var mixed
     */
    protected $result;

    /**
     * @return mixed
     */
    public function result()
    {
        return $this->result;
    }

    /**
     * @param $result
     * @return self
     */
    public function setResult($result)
    {
        $this->result = $result;
        return $this;
    }
}
