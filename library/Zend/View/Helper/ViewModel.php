<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      https://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_View
 */

namespace Zend\View\Helper;

use Zend\View\Model\ModelInterface as Model;

/**
 * Helper for storing and retrieving the root and current view model
 *
 * @package    Zend_View
 * @subpackage Helper
 */
class ViewModel extends AbstractHelper
{
    /**
     * @var Model
     */
    protected $current;

    /**
     * @var Model
     */
    protected $root;

    /**
     * Get the root view model
     *
     * @return null|Model
     */
    public function getRoot()
    {
        return $this->root;
    }

    /**
     * Is a root view model composed?
     *
     * @return bool
     */
    public function hasRoot()
    {
        return ($this->root instanceof Model);
    }

    /**
     * Get the current view model
     *
     * @return null|Model
     */
    public function getCurrent()
    {
        return $this->current;
    }

    /**
     * Is a current view model composed?
     *
     * @return bool
     */
    public function hasCurrent()
    {
        return ($this->current instanceof Model);
    }

    /**
     * Set the root view model
     *
     * @param  Model $model
     * @return ViewModel
     */
    public function setRoot(Model $model)
    {
        $this->root = $model;
        return $this;
    }

    /**
     * Set the current view model
     *
     * @param  Model $model
     * @return ViewModel
     */
    public function setCurrent(Model $model)
    {
        $this->current = $model;
        return $this;
    }
}
