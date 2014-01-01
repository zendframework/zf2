<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Service;

use Zend\Framework\Service\ServicesTrait as Services;
use Zend\Framework\EventManager\EventTrait as Event;

trait EventTrait
{
    /**
     *
     */
    use Event;

    /**
     * @var string
     */
    protected $service;

    /**
     * @var array
     */
    protected $options = [];

    /**
     * @var bool
     */
    protected $shared = true;

    /**
     * @return string
     */
    public function service()
    {
        return $this->service;
    }

    /**
     * @param string $service
     * @return self
     */
    public function setService($service)
    {
        $this->service = $service;
        return $this;
    }

    /**
     * @return bool
     */
    public function shared()
    {
        return $this->shared;
    }

    /**
     * @param $shared
     * @return $this
     */
    public function setShared($shared)
    {
        $this->shared = $shared;
        return $this;
    }

    /**
     * @return array
     */
    public function options()
    {
        return $this->options;
    }

    /**
     * @param array $options
     * @return self
     */
    public function setOptions(array $options)
    {
        $this->options = $options;
        return $this;
    }
}
