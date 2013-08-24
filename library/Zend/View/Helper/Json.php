<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\View\Helper;

use Zend\Http\Response;
use Zend\Http\Headers;
use Zend\Json\Json as JsonFormatter;

/**
 * Helper for simplifying JSON responses
 */
class Json extends AbstractHelper
{
    /**
     * @deprecated Use setHeaders or invoke with the $headers parameter
     *
     * @var Response
     */
    protected $response;

    /**
     * @var Headers
     */
    protected $headers;

    /**
     * Encode data as JSON and set response header
     *
     * @param  mixed $data
     * @param  null|array $jsonOptions Options to pass to JsonFormatter::encode()
     * @param  Headers $headers
     * @return string|void
     */
    public function __invoke($data, array $jsonOptions = null, Headers $headers = null)
    {
        $data = JsonFormatter::encode($data, null, $jsonOptions);

        if($headers !== null)
        {
           $this->setHeaders($headers);
        }

        else if ($this->response instanceof Response) {
            $this->setHeaders($this->response->getHeaders());
            unset($this->response);
        }

        return $data;
    }

    /**
     * @deprecated Use setHeaders or invoke with the $headers parameter
     *
     * Set the response object
     *
     * @param  Response $response
     * @return Json
     */
    public function setResponse(Response $response)
    {
        $this->setHeaders($response->getHeaders());
        return $this;
    }

    /**
     * Set the headers object and add the json Content-Type to it
     *
     * @param  Headers $headers
     * @return Json
     */
    public function setHeaders(Headers $headers)
    {
        if(!$this->headers)
            $this->headers = $headers->addHeaderLine('Content-Type', 'application/json');
        else die('trying to set headers twice');
        return $this;
    }
}
