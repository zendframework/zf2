<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\View\Plugin;

use Zend\Framework\ServiceManager\ServiceRequest;

class Request extends ServiceRequest
{

    /**
     * @return array
     */
    public function getListeners()
    {
        return [[$this, 'factory']];
    }

    /**
     * @param $listener
     * @return mixed
     */
    public function  __invoke($listener)
    {
        return $listener($this);
    }
}
