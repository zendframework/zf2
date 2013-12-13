<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\View;

use Zend\Framework\ServiceManager\Config as ServiceConfig;

class Config
    extends ServiceConfig
{
    /**
     * @param $name
     * @return mixed
     */
    public function getConfig($name)
    {
        return $this->get($name);
    }

    /**
     * @return array
     */
    public function getViewHelpers()
    {
        return (array) $this->get('view_helpers');
    }

    /**
     * @return array
     */
    public function getMvcStrategies()
    {
        return (array) $this->get('mvc_strategies');
    }

    /**
     * @return array
     */
    public function getStrategies()
    {
        return (array) $this->get('strategies');
    }

    /**
     * @return string
     */
    public function getLayoutTemplate()
    {
        return (string) $this->get('layout_template');
    }
}
