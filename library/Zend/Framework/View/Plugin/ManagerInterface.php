<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\View\Plugin;

use Zend\Framework\Service\ManagerInterface as ServiceManagerInterface;

interface ManagerInterface
    extends ServiceManagerInterface
{
    /**
     * @param string $name
     * @param mixed $options
     * @param bool $shared
     * @return null|object
     */
    public function plugin($name, $options = null, $shared = true);
}
