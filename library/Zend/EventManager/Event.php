<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\EventManager;

use Traversable;

/**
 * Representation of an event
 *
 * Encapsulates the target context and parameters passed, and provides some
 * behavior for interacting with the event manager.
 */
class Event implements EventInterface
{
    /**
     * @var string|object The event target
     */
    protected $target;

    /**
     * @var array|ArrayAccess|object The event parameters
     */
    protected $params = array();

    /**
     * @var bool Whether or not to stop propagation
     */
    protected $stopPropagation = false;

    /**
     * Constructor
     *
     * Accept a target and its parameters.
     *
     * @param  string|object     $target
     * @param  array|Traversable $params
     */
    public function __construct($target = null, $params = null)
    {
        $this->setTarget($target);

        if (null !== $params) {
            $this->setParams($params);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function setTarget($target)
    {
        $this->target = $target;
    }

    /**
     * {@inheritDoc}
     *
     * This may be either an object, or the name of a static method.
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * {@inheritDoc}
     */
    public function setParams($params)
    {
        if ($params instanceof Traversable) {
            $params = iterator_to_array($params);
        }

        $this->params = $params;
    }

    /**
     * {@inheritDoc}
     */
    public function setParam($name, $value)
    {
        $this->params[$name] = $value;
    }

    /**
     * {@inheritDoc}
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * {@inheritDoc}
     */
    public function getParam($name, $default = null)
    {
        if (!isset($this->params[$name])) {
            return $default;
        }

        return $this->params[$name];
    }

    /**
     * {@inheritDoc}
     */
    public function stopPropagation($flag = true)
    {
        $this->stopPropagation = (bool) $flag;
    }

    /**
     * {@inheritDoc}
     */
    public function isPropagationStopped()
    {
        return $this->stopPropagation;
    }
}
