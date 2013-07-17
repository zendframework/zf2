<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\View\Model;

use Countable;
use IteratorAggregate;
use Traversable;

/**
 * Interface describing a view model.
 *
 * Extends "Countable"; count() should return the number of children attached
 * to the model.
 *
 * Extends "IteratorAggregate"; should allow iterating over children.
 */
interface ModelInterface extends Countable, IteratorAggregate
{
    /**
     * Set options
     *
     * @param array|Traversable|AbstractModelOptions $options
     * @return ModelInterface Fluent interface
     */
    public function setOptions($options);

    /**
     * Get options
     *
     * @return AbstractModelOptions
     */
    public function getOptions();

    /**
     * Get a single variable
     *
     * @param  string       $name
     * @param  mixed|null   $default (optional) default value if the variable is not present.
     * @return mixed
     */
    public function getVariable($name, $default = null);

    /**
     * Set a single variable
     *
     * @param  string $name
     * @param  mixed $value
     * @return ModelInterface
     */
    public function setVariable($name, $value);

    /**
     * Set all variables
     *
     * @param  array|\ArrayAccess $variables
     * @return ModelInterface
     */
    public function setVariables($variables);

    /**
     * Get all variables
     *
     * @return array|\ArrayAccess
     */
    public function getVariables();

    /**
     * Add a child model
     *
     * @param  ModelInterface $child
     * @param  null|string $captureTo Optional; if specified, the "capture to" value to set on the child
     * @param  null|bool $append Optional; if specified, append to child  with the same capture
     * @return ModelInterface
     */
    public function addChild(ModelInterface $child, $captureTo = null, $append = false);

    /**
     * Return all children.
     *
     * Return specifies an array, but may be any iterable object.
     *
     * @return array
     */
    public function getChildren();

    /**
     * Does the model have any children?
     *
     * @return bool
     */
    public function hasChildren();
}
