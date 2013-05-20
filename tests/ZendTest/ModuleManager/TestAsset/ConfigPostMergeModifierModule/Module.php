<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_ModuleManager
 */

namespace ConfigPostMergeModifierModule;

use Zend\Config\Config;
use Zend\ModuleManager\Feature\ConfigPostMergeModifierInterface;

class Module implements ConfigPostMergeModifierInterface
{
    public function getConfig()
    {
        return new Config(include __DIR__ . '/configs/config.php');
    }

    public function modifyConfigPostMerge(array $config) {
        unset($config['some']);
        return $config;
    }
}
