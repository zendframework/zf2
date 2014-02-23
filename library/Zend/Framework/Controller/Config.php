<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Controller;

use Zend\Framework\Config\ConfigInterface as Serializable;
use Zend\Framework\Event\Manager\ConfigInterface as EventListener;
use Zend\Framework\Event\Manager\ConfigTrait;

class Config
    implements ConfigInterface, EventListener, Serializable
{
    /**
     *
     */
    use ConfigTrait;
}
