<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\I18n\Translator;

use Zend\Framework\Config\ConfigTrait as ConfigTrait;
use Zend\Framework\Config\ConfigInterface as Serializable;

class Config
    implements ConfigInterface, Serializable
{
    /**
     *
     */
    use ConfigTrait;
}
