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
use Zend\View\Helper\Doctype as DoctypeHelper;
use Zend\Framework\Service\ServiceInterface;

class Doctype
    extends DoctypeHelper
    implements ServiceInterface
{
    /**
     * @param ServiceManager $sm
     * @return static
     */
    public function __service(ServiceManager $sm)
    {
        $config = $sm->config()->view();

        if ($config->docType()) {
            $this->setDoctype($config->docType());
        }
    }
}
