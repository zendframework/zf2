<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Validator;

class StaticValidator
{
    /**
     * @var ValidatorPluginManager
     */
    protected static $plugins;

    /**
     * Set plugin manager to use for locating validators
     *
     * @param  ValidatorPluginManager $plugins
     * @return void
     */
    public static function setPluginManager(ValidatorPluginManager $plugins)
    {
        static::$plugins = $plugins;
    }

    /**
     * Get plugin manager for locating validators
     *
     * @return ValidatorPluginManager
     */
    public static function getPluginManager()
    {
        if (null === static::$plugins) {
            static::setPluginManager(new ValidatorPluginManager());
        }

        return static::$plugins;
    }

    /**
     * Execute the validator
     *
     * @param  mixed    $data
     * @param  string   $validatorName
     * @param  array    $args
     * @return Result\ValidationResultInterface
     */
    public static function execute($data, $validatorName, array $args = array())
    {
        $plugins   = static::getPluginManager();
        $validator = $plugins->get($validatorName, $args);

        return $validator->isValid($data);
    }
}
