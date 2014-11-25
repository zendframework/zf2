<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Crypt\Password;

use Zend\ServiceManager\AbstractPluginManager;

/**
 * Plugin manager for {@see HandlerAggregate}.
 */
class HandlerManager extends AbstractPluginManager
{
    /**
     * $invokableClasses: defined by AbstractPluginManager.
     *
     * @see AbstractPluginManager::$invokableClasses
     * @var array
     */
    protected $invokableClasses = array(
        'simplemd5'  => 'Zend\Crypt\Password\Algorithm\SimpleMd5',
        'simplesha1' => 'Zend\Crypt\Password\Algorithm\SimpleSha1',
    );

    /**
     * $factories: defined by AbstractPluginManager.
     *
     * @see AbstractPluginManager::$factories
     * @var array
     */
    protected $factories = array(
        'bcrypt' => 'Zend\Crypt\Password\Algorithm\Factory\BcryptFactory',
    );

    /**
     * {@inheritDoc}
     */
    public function validatePlugin($plugin)
    {
        if ($plugin instanceof HandlerInterface) {
            return;
        }

        throw new Exception\RuntimeException(sprintf(
            'Plugin of type %s is invalid; must implement %s\HandlerInterface',
            (is_object($plugin) ? get_class($plugin) : gettype($plugin)),
            __NAMESPACE__
        ));
    }
}
