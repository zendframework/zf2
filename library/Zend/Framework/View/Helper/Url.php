<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\View\Helper;

use Zend\Framework\ServiceManager\ServiceManagerInterface as ServiceManager;

use Zend\View\Helper\Url as UrlHelper;
use Zend\Framework\ServiceManager\FactoryInterface;

class Url
    extends UrlHelper
    implements FactoryInterface
{
    /**
     * @param ServiceManager $sm
     * @return static
     */
    public function createService(ServiceManager $sm)
    {
        $this->setRouter($sm->getRouter());
        $this->setRouteMatch($sm->getRouteMatch());
        return $this;
    }
}
