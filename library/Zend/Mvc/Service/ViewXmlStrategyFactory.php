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
use Zend\View\Strategy\XmlStrategy;

/**
 * @category   Zend
 * @package    Zend_Mvc
 * @subpackage Service
 */
class ViewXmlStrategyFactory implements FactoryInterface
{
    /**
     * Create and return the Xml view strategy
     *
     * Retrieves the ViewXmlRenderer service from the service locator, and
     * injects it into the constructor for the xml strategy.
     *
     * @param  ServiceLocatorInterface $serviceLocator
     * @return XmlStrategy
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $feedRenderer = $serviceLocator->get('ViewXmlRenderer');
        $feedStrategy = new XmlStrategy($feedRenderer);
        return $feedStrategy;
    }
}
