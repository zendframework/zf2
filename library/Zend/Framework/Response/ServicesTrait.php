<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Response;

use Zend\Stdlib\ResponseInterface as Response;

trait ServicesTrait
{
    /**
     * @return bool|object
     */
    public function response()
    {
        return $this->sm->get('Response');
    }

    /**
     * @param Response $response
     * @return self
     */
    public function setResponse(Response $response)
    {
        $this->sm->add('Response', $response);
        return $this;
    }

    /**
     * @param $content
     * @return self
     */
    public function setResponseContent($content)
    {
        $this->response()->setContent($content);
        return $this;
    }
}
