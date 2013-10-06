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
    protected static $validatorPluginManager;

    /**
     * Set plugin manager to use for locating validators
     *
     * @param  ValidatorPluginManager $validatorPluginManager
     * @return void
     */
    public static function setPluginManager(ValidatorPluginManager $validatorPluginManager)
    {
        static::$validatorPluginManager = $validatorPluginManager;
    }

    /**
     * Get plugin manager for locating validators
     *
     * @return ValidatorPluginManager
     */
    public static function getPluginManager()
    {
        if (null === static::$validatorPluginManager) {
            static::setPluginManager(new ValidatorPluginManager());
        }

        return static::$validatorPluginManager;
    }

    /**
     * Execute the validator
     *
     * @param  mixed    $data
     * @param  string   $validatorName
     * @param  array    $options
     * @return Result\ValidationResultInterface
     */
    public static function execute($data, $validatorName, array $options = array())
    {
        $validator = static::getPluginManager()->get($validatorName, $options);

        return $validator->validate($data);
    }
}
