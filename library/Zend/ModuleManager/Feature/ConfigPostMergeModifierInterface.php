<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\ModuleManager\Feature;

interface ConfigPostMergeModifierInterface
{
    /**
     * Allows a module to modify the application configuration after it's own
     * configuration has been merged. In particular, allows the removal of
     * application config keys.
     *
     * @param array $config
     * @return array
     */
    public function modifyConfigPostMerge(array $config);
}
