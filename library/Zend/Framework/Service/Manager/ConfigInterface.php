<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Service\Manager;

use Zend\Framework\Config\ConfigInterface as Config;
use Zend\Framework\Service\Config\ConfigInterface as Services;

interface ConfigInterface
    extends Config
{
    /**
     * @return Services
     */
    public function services();
}
