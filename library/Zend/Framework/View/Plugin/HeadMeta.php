<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\View\Plugin;

use Zend\Framework\Service\Manager\ManagerInterface as ServiceManager;
use Zend\View\Helper\HeadMeta as HeadMetaHelper;
use Zend\Framework\Service\ServiceInterface;

class HeadMeta
    extends HeadMetaHelper
    implements ServiceInterface
{
    /**
     * @param ServiceManager $sm
     * @return static
     */
    public function __service(ServiceManager $sm)
    {
        $this->setView($sm->get('View\Renderer'));
    }
}
