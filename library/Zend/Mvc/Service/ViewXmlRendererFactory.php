<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Mvc
 */

namespace Zend\Mvc\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\View\Renderer\XmlRenderer;
use Zend\View\Resolver\AggregateResolver;
use Zend\View\Resolver\TemplatePathStack;
use Zend\View\Resolver\TemplateMapResolver;

/**
 * @category   Zend
 * @package    Zend_Mvc
 * @subpackage Service
 */
class ViewXmlRendererFactory implements FactoryInterface
{
    /**
     * Create and return the feed view renderer
     *
     * @param  ServiceLocatorInterface $serviceLocator
     * @return XmlRenderer
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $renderer = new XmlRenderer();
        $renderer->setHelperPluginManager($serviceLocator->get('ViewHelperManager'));

        $resolver = new AggregateResolver();
        $resolver->attach($serviceLocator->get('ViewXmlTemplatePathStack'));
        $resolver->attach($serviceLocator->get('ViewXmlTemplateMapResolver'));

        $renderer->setResolver($resolver);

        return $renderer;
    }
}

