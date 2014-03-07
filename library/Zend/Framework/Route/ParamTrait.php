<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Route;

trait ParamTrait
{
    /**
     * Default parameters.
     *
     * @var array
     */
    protected $defaultParams = [];

    /**
     * Set a default parameters.
     *
     * @param  array $params
     * @return self
     */
    public function setDefaultParams(array $params)
    {
        $this->defaultParams = $params;
        return $this;
    }

    /**
     * Set a default parameter.
     *
     * @param  string $name
     * @param  mixed  $value
     * @return self
     */
    public function setDefaultParam($name, $value)
    {
        $this->defaultParams[$name] = $value;
        return $this;
    }
}
