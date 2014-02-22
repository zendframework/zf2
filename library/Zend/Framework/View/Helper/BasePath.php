<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\View\Helper;

use Zend\Framework\Service\ManagerInterface as ServiceManager;
use Zend\View\Helper\BasePath as BasePathHelper;
use Zend\Framework\Service\ServiceInterface;

class BasePath
    extends BasePathHelper
    implements ServiceInterface
{
    /**
     * @param ServiceManager $sm
     * @return static
     */
    public function __service(ServiceManager $sm)
    {
        $config = $sm->get('View\Config');

        if ($config && $config->get('base_path')) {
            $this->setBasePath($config->get('base_path'));
            return;
        }

        $request = $sm->get('Request');

        if (is_callable(array($request, 'getBasePath'))) {
            $this->setBasePath($request->getBasePath());
        }
    }
}
