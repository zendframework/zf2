<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Navigation
 */

namespace Zend\Navigation\View;

use Zend\ServiceManager\ConfigInterface;
use Zend\ServiceManager\ServiceManager;

/**
 * Service manager configuration for navigation view helpers
 *
 * @category   Zend
 * @package    Zend_Navigation
 * @subpackage View
 */
class HelperConfig implements ConfigInterface
{
    /**
     * @var array Pre-aliased view helpers
     */
    protected $invokables = array(
        'navigation'        => 'Zend\Navigation\View\Helper\Navigation',
        'breadcrumbs'       => 'Zend\Navigation\View\Helper\Breadcrumbs',
        'links'             => 'Zend\Navigation\View\Helper\Links',
        'menu'              => 'Zend\Navigation\View\Helper\Menu',
        'sitemap'           => 'Zend\Navigation\View\Helper\Sitemap',
    );

    /**
     * Configure the provided service manager instance with the configuration
     * in this class.
     *
     * Adds the invokables defined in this class to the SM managing helpers.
     *
     * @param  ServiceManager $serviceManager
     * @return void
     */
    public function configureServiceManager(ServiceManager $serviceManager)
    {
        foreach ($this->invokables as $name => $invokable) {
            $serviceManager->setFactory($name, function ($sm) use ($invokable) {
                $service = new $invokable();
                $service->setServiceLocator($sm);

                return $service;
            });
        }
    }
}
