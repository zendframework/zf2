<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Response;

use Zend\Stdlib\ResponseInterface as Response;

trait ServiceTrait
{
    /**
     * @var Response
     */
    protected $response;

    /**
     * @return Response
     */
    public function response()
    {
        return $this->response;
    }

    /**
     * @param Response $response
     * @return self
     */
    public function setResponse(Response $response)
    {
        $this->response = $response;
        return $this;
    }

    /**
     * @param $content
     * @return self
     */
    public function setResponseContent($content)
    {
        $this->response->setContent($content);
        return $this;
    }
}
