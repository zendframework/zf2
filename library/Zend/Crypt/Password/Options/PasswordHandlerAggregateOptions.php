<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Crypt\Password\Options;

use Zend\Crypt\Password\HandlerAggregate;
use Zend\Stdlib\AbstractOptions;

/**
 * @see PasswordHandlerAggregateOptionsInterface
 */
class PasswordHandlerAggregateOptions extends AbstractOptions implements PasswordHandlerAggregateOptionsInterface
{
    /**
     * @var array
     */
    protected $hashingMethods = array(
        'Bcrypt',
        'SimpleSha1',
        'SimpleMd5',
    );

    /**
     * @var string
     */
    protected $defaultHashingMethod = 'bcrypt';

    /**
     * @var bool
     */
    protected $migrateToDefaultHashingMethod = true;

    /**
     * @return array
     */
    public function getHashingMethods()
    {
        return $this->hashingMethods;
    }

    /**
     * @param  array $hashingMethods
     *
     * @return HandlerAggregate
     */
    public function setHashingMethods(array $hashingMethods)
    {
        $this->hashingMethods = $hashingMethods;
        return $this;
    }

    /**
     * @return string
     */
    public function getDefaultHashingMethod()
    {
        return $this->defaultHashingMethod;
    }

    /**
     * @param  string $defaultHashingMethod
     *
     * @return HandlerAggregate
     */
    public function setDefaultHashingMethod($defaultHashingMethod)
    {
        $this->defaultHashingMethod = $defaultHashingMethod;
        return $this;
    }

    /**
     * @return bool
     */
    public function getMigrateToDefaultHashingMethod()
    {
        return $this->migrateToDefaultHashingMethod;
    }

    /**
     * @param  bool $migrateToDefaultHashingMethod
     *
     * @return HandlerAggregate
     */
    public function setMigrateToDefaultHashingMethod($migrateToDefaultHashingMethod)
    {
        $this->migrateToDefaultHashingMethod = (bool) $migrateToDefaultHashingMethod;
        return $this;
    }
}
