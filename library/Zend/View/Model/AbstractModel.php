<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\View\Model;

use ArrayAccess;
use ArrayIterator;
use Traversable;
use Zend\View\Exception;
use Zend\Stdlib\ArrayUtils;
use Zend\View\Variables as ViewVariables;

abstract class AbstractModel implements ModelInterface, ClearableModelInterface
{
    /**
     * Child models
     *
     * @var array
     */
    protected $children = array();

    /**
     * View variables
     *
     * @var array|ArrayAccess|Traversable
     */
    protected $variables = array();

    /**
     * Property overloading: set variable value
     *
     * @param  string $name
     * @param  mixed $value
     * @return void
     */
    public function __set($name, $value)
    {
        $this->setVariable($name, $value);
    }

    /**
     * Property overloading: get variable value
     *
     * @param  string $name
     * @return mixed
     */
    public function __get($name)
    {
        if (!$this->__isset($name)) {
            return null;
        }

        $variables = $this->getVariables();

        return $variables[$name];
    }

    /**
     * Property overloading: do we have the requested variable value?
     *
     * @param  string $name
     * @return bool
     */
    public function __isset($name)
    {
        $variables = $this->getVariables();

        return isset($variables[$name]);
    }

    /**
     * Property overloading: unset the requested variable
     *
     * @param  string $name
     * @return void
     */
    public function __unset($name)
    {
        if (!$this->__isset($name)) {
            return null;
        }

        unset($this->variables[$name]);
    }

    /**
     * Get a single variable
     *
     * @param  string       $name
     * @param  mixed|null   $default (optional) default value if the variable is not present.
     * @return mixed
     */
    public function getVariable($name, $default = null)
    {
        $name = (string) $name;
        if (array_key_exists($name, $this->variables)) {
            return $this->variables[$name];
        }

        return $default;
    }

    /**
     * Set a single variable
     *
     * @param  string $name
     * @param  mixed $value
     * @return AbstractModel
     */
    public function setVariable($name, $value)
    {
        $this->variables[(string) $name] = $value;

        return $this;
    }

    /**
     * Set variables en masse
     *
     * Can be an array or a Traversable + ArrayAccess object.
     *
     * @param  array|ArrayAccess|Traversable $variables
     * @param  bool $overwrite Whether or not to overwrite the internal container with $variables
     * @throws Exception\InvalidArgumentException
     * @return AbstractModel
     */
    public function setVariables($variables, $overwrite = false)
    {
        if (!is_array($variables) && !$variables instanceof Traversable) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s: expects an array, or Traversable argument; received "%s"',
                __METHOD__,
                (is_object($variables) ? get_class($variables) : gettype($variables))
            ));
        }

        if ($overwrite) {
            if (is_object($variables) && !$variables instanceof ArrayAccess) {
                $variables = ArrayUtils::iteratorToArray($variables);
            }

            $this->variables = $variables;
            return $this;
        }

        foreach ($variables as $key => $value) {
            $this->setVariable($key, $value);
        }

        return $this;
    }

    /**
     * Get all variables
     *
     * @return array|ArrayAccess|Traversable
     */
    public function getVariables()
    {
        return $this->variables;
    }

    /**
     * Clear all variables
     *
     * Resets the internal variable container to an empty container.
     *
     * @return AbstractModel
     */
    public function clearVariables()
    {
        $this->variables = new ViewVariables();

        return $this;
    }

    /**
     * Add a child model
     *
     * @param  ModelInterface $child
     * @param  null|string $captureTo Optional; if specified, the "capture to" value to set on the child
     * @param  null|bool $append Optional; if specified, append to child  with the same capture
     * @return AbstractModel
     */
    public function addChild(ModelInterface $child, $captureTo = null, $append = null)
    {
        $this->children[] = $child;
        if (null !== $captureTo) {
            $child->getOptions()->setCaptureTo($captureTo);
        }
        if (null !== $append) {
            $child->getOptions()->setAppend($append);
        }

        return $this;
    }

    /**
     * Return all children.
     *
     * Return specifies an array, but may be any iterable object.
     *
     * @return array
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Does the model have any children?
     *
     * @return bool
     */
    public function hasChildren()
    {
        return (0 < count($this->children));
    }

    /**
     * Clears out all child models
     *
     * @return AbstractModel
     */
    public function clearChildren()
    {
        $this->children = array();

        return $this;
    }

    /**
     * Return count of children
     *
     * @return int
     */
    public function count()
    {
        return count($this->children);
    }

    /**
     * Get iterator of children
     *
     * @return ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->children);
    }
}
