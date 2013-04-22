<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Form;

use Zend\ServiceManager\AbstractPluginManager;
use Zend\Form\Exception;

/**
 * Plugin manager implementation for binded form objects.
 *
 * Validates objects for *object* type.
 */
class FormObjectManager extends AbstractPluginManager
{
    /**
     * @see \Zend\ServiceManager\AbstractPluginManager::validatePlugin()
     */
    public function validatePlugin ($plugin)
    {
        if (!is_object($plugin)) { 
            throw new Exception\InvalidElementException(
                sprintf('Expected object, "%s" actual', gettype($plugin)));
        }
    }
}
