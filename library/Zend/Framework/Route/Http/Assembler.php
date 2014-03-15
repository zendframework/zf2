<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Route\Http;

use Zend\Framework\Route\Assemble\AssembleInterface;
use Zend\Framework\Route\Assemble\AssemblerInterface;
use Zend\Mvc\Router\Exception;
use Zend\Uri\Http as HttpUri;

class Assembler
    implements AssemblerInterface
{
    /**
     * @var string
     */
    protected $baseUrl;

    /**
     * @var array
     */
    protected $defaultParams;

    /**
     * @var HttpUri
     */
    protected $requestUri;

    /**
     * @param HttpUri $requestUri
     * @param string $baseUrl
     * @param array $defaultParams
     */
    public function __construct(HttpUri $requestUri, $baseUrl, array $defaultParams = [])
    {
        $this->baseUrl       = $baseUrl;
        $this->defaultParams = $defaultParams;
        $this->requestUri    = $requestUri;
    }

    /**
     * @param AssembleInterface $route
     * @param array $params
     * @param array $options
     * @return mixed|string
     * @throws Exception\InvalidArgumentException
     * @throws Exception\RuntimeException
     */
    public function build(AssembleInterface $route, array $params = [], array $options = [])
    {
        if (isset($options['only_return_path']) && $options['only_return_path']) {
            return $this->baseUrl . $route->assemble(array_merge($this->defaultParams, $params), $options);
        }

        if (!isset($options['uri'])) {
            $uri = new HttpUri();

            if (isset($options['force_canonical']) && $options['force_canonical']) {
                if ($this->requestUri === null) {
                    throw new Exception\RuntimeException('Request URI has not been set');
                }

                $uri->setScheme($this->requestUri->getScheme())
                    ->setHost($this->requestUri->getHost())
                    ->setPort($this->requestUri->getPort());
            }

            $options['uri'] = $uri;
        } else {
            $uri = $options['uri'];
        }

        $path = $this->baseUrl . $route->assemble(array_merge($this->defaultParams, $params), $options);

        if (isset($options['query'])) {
            $uri->setQuery($options['query']);
        }

        if (isset($options['fragment'])) {
            $uri->setFragment($options['fragment']);
        }

        if ((isset($options['force_canonical']) && $options['force_canonical']) || $uri->getHost() !== null || $uri->getScheme() !== null) {
            if (($uri->getHost() === null || $uri->getScheme() === null) && $this->requestUri === null) {
                throw new Exception\RuntimeException('Request URI has not been set');
            }

            if ($uri->getHost() === null) {
                $uri->setHost($this->requestUri->getHost());
            }

            if ($uri->getScheme() === null) {
                $uri->setScheme($this->requestUri->getScheme());
            }

            return $uri->setPath($path)->normalize()->toString();
        } elseif (!$uri->isAbsolute() && $uri->isValidRelative()) {
            return $uri->setPath($path)->normalize()->toString();
        }

        return $path;
    }
}
