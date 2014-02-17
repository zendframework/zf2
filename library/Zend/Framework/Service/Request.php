<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Service;

class Request
    implements RequestInterface
{
    /**
     * @param string $alias
     * @param bool $shared
     */
    public function __construct($alias, $shared = true)
    {
        $this->alias   = $alias;
        $this->shared  = $shared;
    }

    /**
     * @return string
     */
    public function alias()
    {
        return $this->alias;
    }

    /**
     * @param callable $factory
     * @param array $options
     * @return mixed
     */
    public function call(callable $factory, array $options = [])
    {
        return $this->__invoke($factory, $options);
    }

    /**
     * @return bool
     */
    public function shared()
    {
        return $this->shared;
    }

    /**
     * @param callable $factory
     * @param array $options
     * @return mixed
     */
    public function __invoke(callable $factory, array $options = [])
    {
        return $factory($this, $options);
    }
}
