<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Controller;

use Zend\Framework\Config\ConfigTrait as SerialConfigTrait;
use Zend\Framework\Config\ConfigInterface as Serializable;

class Config
    implements ConfigInterface, ConfigServiceInterface, Serializable
{
    /**
     *
     */
    use ConfigServiceTrait,
        SerialConfigTrait;
}
