<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Request;

use Zend\Stdlib\RequestInterface as Request;

trait ServicesTrait
{
    /**
     * @return Request
     */
    public function request()
    {
        return $this->sm->get('Request');
    }

    /**
     * @param Request $request
     * @return self
     */
    public function setRequest(Request $request)
    {
        return $this->sm->add('Request', $request);
    }
}
