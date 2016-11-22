<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\I18n\Translator\Manager;

use Zend\Framework\Config\ConfigInterface as Config;
use Zend\Framework\I18n\Translator\Config\ConfigInterface as Translator;

interface ConfigInterface
    extends Config
{
    /**
     * @return Translator
     */
    public function translator();
}
