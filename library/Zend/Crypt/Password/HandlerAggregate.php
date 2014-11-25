<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Crypt\Password;

use Zend\Crypt\Password\Options\PasswordHandlerAggregateOptions;
use Zend\Crypt\Password\Options\PasswordHandlerAggregateOptionsInterface;

/**
 * Aggregate password handler for using multiple hashing methods parallely.
 */
class HandlerAggregate implements HandlerInterface
{
    /**
     * @var HandlerManager
     */
    protected $handlerManager;

    /**
     * @var array
     */
    protected $hashCache = array();

    /**
     * @var array
     */
    protected $handlerCache = array();

    /**
     * @var PasswordHandlerAggregateOptionsInterface
     */
    protected $options;

    /**
     * {@inheritDoc}
     */
    public function supports($hash)
    {
        return ($this->getHandlerByHash($hash) !== null);
    }

    /**
     * {@inheritDoc}
     */
    public function hash($password)
    {
        return $this->getHandlerByName($this->getOptions()->getDefaultHashingMethod())->hash($password);
    }

    /**
     * {@inheritDoc}
     */
    public function compare($password, $hash)
    {
        $handler = $this->getHandlerByHash($hash);

        if ($handler === null) {
            return false;
        }

        return $handler->compare($password, $hash);
    }

    /**
     * {@inheritDoc}
     */
    public function shouldRehash($hash)
    {
        $handler = $this->getHandlerByHash($hash);

        if ($handler === null) {
            // Hash is not uspported by any method, migration recommended.
            return true;
        }

        if ($handler->shouldRehash($hash)) {
            return true;
        }

        if ($this->getOptions()->getMigrateToDefaultHashingMethod()) {
            $defaultHandler = $this->getHandlerByName($this->getOptions()->getDefaultHashingMethod());

            if ($handler !== $defaultHandler) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return HandlerManager
     */
    public function getHandlerManager()
    {
        if ($this->handlerManager === null) {
            $this->setHandlerManager(new HandlerManager());
        }

        return $this->handlerManager;
    }

    /**
     * @param  HandlerManager $handlerManager
     *
     * @return HandlerAggregate
     */
    public function setHandlerManager(HandlerManager $handlerManager)
    {
        $this->handlerManager = $handlerManager;
        return $this;
    }

    /**
     * @return PasswordHandlerAggregateOptionsInterface
     */
    public function getOptions()
    {
        if ($this->options === null) {
            $this->setOptions(new PasswordHandlerAggregateOptions());
        }

        return $this->options;
    }

    /**
     * @param  PasswordHandlerAggregateOptionsInterface $options
     *
     * @return HandlerAggregate
     */
    public function setOptions(PasswordHandlerAggregateOptionsInterface $options)
    {
        $this->options = $options;
        return $this;
    }

    /**
     * @param  string $hashingMethod
     *
     * @return HandlerInterface
     */
    protected function getHandlerByName($hashingMethod)
    {
        if (!isset($this->hashCache[$hashingMethod])) {
            $this->hashCache[$hashingMethod] = $this->getHandlerManager()->get($hashingMethod);
        }

        return $this->hashCache[$hashingMethod];
    }

    /**
     * @param  string $hash
     *
     * @return HandlerInterface
     */
    protected function getHandlerByHash($hash)
    {
        foreach ($this->getOptions()->getHashingMethods() as $hashingMethod) {
            $handler = $this->getHandlerByName($hashingMethod);

            if ($handler->supports($hash)) {
                return $handler;
            }
        }

        return null;
    }
}
