<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\View\HelperFactory;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\View\Helper\Doctype as DoctypeHelper;
use Zend\View\HelperPluginManager;

/**
 * Class DoctypeFactory
 */
class DoctypeFactory implements FactoryInterface
{
    /**
     * Create bas
     *
     * @param ServiceLocatorInterface|HelperPluginManager $helperManager
     *
     * @return \Zend\View\Helper\Doctype
     */
    public function createService(ServiceLocatorInterface $helperManager)
    {
        $sl = $helperManager->getServiceLocator();

        $config = $sl->has('Config') ? $sl->get('Config') : array();
        $config = isset($config['view_manager']) ? $config['view_manager'] : array();

        $doctypeHelper = new DoctypeHelper;
        if (isset($config['doctype']) && $config['doctype']) {
            $doctypeHelper->setDoctype($config['doctype']);
        }

        return $doctypeHelper;
    }
}
