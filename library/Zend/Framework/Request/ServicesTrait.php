<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Request;

use Zend\Http\PhpEnvironment\Request as Request;

trait ServicesTrait
{
    /**
     * @return bool|Request
     */
    public function request()
    {
        return $this->service('Request');
    }

    /**
     * @param Request $request
     * @return self
     */
    public function setRequest(Request $request)
    {
        return $this->add('Request', $request);
    }
}
