<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\View\Helper;

use Zend\Framework\Service\ListenerInterface as ServiceManager;

use Zend\View\Helper\Url as UrlHelper;
use Zend\Framework\Service\ServiceInterface;

class Url
    extends UrlHelper
    implements ServiceInterface
{
    /**
     * @param ServiceManager $sm
     * @return static
     */
    public function __service(ServiceManager $sm)
    {
        $this->setRouter($sm->router());
        $this->setRouteMatch($sm->routeMatch());
        return $this;
    }
}
