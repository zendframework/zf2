<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Route\Manager;

use Zend\Framework\Event\ListenerInterface as Listener;
use Zend\Framework\Service\RequestInterface as Request;

interface ListenerInterface
    extends Listener
{
    /**
     * Trigger
     *
     * @param Request $request
     * @return mixed
     */
    public function trigger(Request $request);
}
