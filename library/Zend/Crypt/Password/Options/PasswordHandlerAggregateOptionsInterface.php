<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Crypt\Password\Options;

/**
 * Options for the password aggregate handler.
 */
interface PasswordHandlerAggregateOptionsInterface
{
    /**
     * @return array
     */
    public function getHashingMethods();

    /**
     * @param  array $hashingMethods
     *
     * @return PasswordHandlerAggregateOptionsInterface
     */
    public function setHashingMethods(array $hashingMethods);

    /**
     * @return string
     */
    public function getDefaultHashingMethod();

    /**
     * @param  string $defaultHashingMethod
     *
     * @return PasswordHandlerAggregateOptionsInterface
     */
    public function setDefaultHashingMethod($defaultHashingMethod);

    /**
     * @return bool
     */
    public function getMigrateToDefaultHashingMethod();

    /**
     * @param  bool $migrateToDefaultHashingMethod
     *
     * @return PasswordHandlerAggregateOptionsInterface
     */
    public function setMigrateToDefaultHashingMethod($migrateToDefaultHashingMethod);
}
