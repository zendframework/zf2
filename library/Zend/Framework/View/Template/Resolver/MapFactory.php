<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\View\Template\Resolver;

use Zend\Framework\Service\ListenerFactoryInterface as FactoryInterface;
use Zend\Framework\Service\ListenerInterface as ServiceManager;
use Zend\View\Resolver\TemplateMapResolver;


class MapFactory implements FactoryInterface
{
    /**
     * Create the template map view resolver
     *
     * Creates a Zend\View\Resolver\AggregateResolver and populates it with the
     * ['view_manager']['template_map']
     *
     * @param  ServiceManager $sm
     * @return TemplateMapResolver
     */
    public function createService(ServiceManager $sm)
    {
        $config = $sm->applicationConfig();
        $map = array();
        if (is_array($config) && isset($config['view_manager'])) {
            $config = $config['view_manager'];
            if (is_array($config) && isset($config['template_map'])) {
                $map = $config['template_map'];
            }
        }
        return new TemplateMapResolver($map);
    }
}
