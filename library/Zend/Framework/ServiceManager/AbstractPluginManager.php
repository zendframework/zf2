<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\ServiceManager;

use Zend\Framework\ServiceManager\ServiceManagerInterface as ServiceManager;
use Zend\Framework\View\Plugin\Request as PluginRequest;

use Zend\I18n\Translator\TranslatorAwareInterface as Translator;
use Zend\Framework\ServiceManager\ConfigInterface as Config;

abstract class AbstractPluginManager
{
    protected $config;

    protected $sm;

    public function __construct(ServiceManager $sm, Config $config)
    {
        $this->sm = $sm;
        $this->config = $config;
    }

    public function getAlias($name)
    {
        return $this->config->get(strtolower($name));
    }

    public function get(PluginRequest $request)
    {
        return $this->sm->get(new ServiceRequest($this->getAlias($request->getName())));
    }

    public function add($name, $service)
    {
        $this->sm->add($this->getAlias($name), $service);
        return $this;
    }

    /**
     * @param string $name
     * @param string $class
     */
    public function addInvokableClass($name, $class)
    {
        $this->sm->addInvokableClass($name, $class);
    }
}
