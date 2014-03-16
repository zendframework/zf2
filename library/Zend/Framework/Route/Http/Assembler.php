<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Route\Http;

use Zend\Framework\Route\Http\Part\PartInterface;
use Zend\Framework\Route\Assemble\AssemblerInterface;
use Zend\Framework\Route\Manager\ManagerInterface as RouteManager;
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
     * @var RouteManager
     */
    protected $rm;

    /**
     * @param RouteManager $rm
     * @param HttpUri $requestUri
     * @param string $baseUrl
     * @param array $defaultParams
     */
    public function __construct(RouteManager $rm, HttpUri $requestUri, $baseUrl, array $defaultParams = [])
    {
        $this->baseUrl       = $baseUrl;
        $this->defaultParams = $defaultParams;
        $this->requestUri    = $requestUri;
        $this->rm            = $rm;
    }

    /**
     * @param array $params
     * @param array $options
     * @return mixed
     * @throws Exception\InvalidArgumentException
     * @throws Exception\RuntimeException
     */
    public function url(array $params = [], array $options = [])
    {
        if (!isset($options['name'])) {
            throw new Exception\InvalidArgumentException('Missing "name" option');
        }

        $name = explode('/', $options['name'], 2);

        list($name, $children) = [$name[0], isset($name[1]) ? $name[1] : null];

        $route = $this->rm->routes()->routes()->get($name);

        $route = $this->rm->route($route['type'], $route);

        if (!$route) {
            throw new Exception\RuntimeException(sprintf('Route with name "%s" not found', $name));
        }

        if ($children) {

            if (!$route instanceof PartInterface) {
                throw new Exception\RuntimeException(sprintf('Route with name "%s" does not have child routes', $name));
            }

            $options['name'] = $children;

        } else {

            unset($options['name']);

        }

        if (!empty($options['only_return_path'])) {
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
