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

use Zend\View\Helper\HeadMeta as HeadMetaHelper;
use Zend\Framework\ServiceManager\FactoryInterface;

class HeadMeta
    extends HeadMetaHelper
    implements FactoryInterface
{
    /**
     * @param ServiceManager $sm
     * @return static
     */
    public function createService(ServiceManager $sm)
    {
        $this->setView($sm->getViewRenderer());
        return $this;
    }
}
