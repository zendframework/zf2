<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Service;

class Request
    implements RequestInterface
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var bool
     */
    protected $shared = true;

    /**
     * @param string $name
     * @param bool $shared
     */
    public function __construct($name, $shared = true)
    {
        $this->name   = $name;
        $this->shared = $shared;
    }

    /**
     * @return string
     */
    public function name()
    {
        return $this->name;
    }

    /**
     * @param callable $factory
     * @param array $options
     * @return mixed
     */
    public function service(callable $factory, array $options = [])
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
