<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Server
 * @subpackage Zend_Server_Reflection
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Server\Reflection;

/**
 * Node Tree class for Zend_Server reflection operations
 *
 * @category   Zend
 * @package    Zend_Server
 * @subpackage Zend_Server_Reflection
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Node
{
    /**
     * Node value
     * @var mixed
     */
    protected $_value = null;

    /**
     * Array of child nodes (if any)
     * @var array
     */
    protected $_children = array();

    /**
     * Parent node (if any)
     * @var \Zend\Server\Reflection\Node
     */
    protected $_parent = null;

    /**
     * Constructor
     *
     * @param mixed $value
     * @param \Zend\Server\Reflection\Node $parent Optional
     * @return \Zend\Server\Reflection\Node
     */
    public function __construct($value, Node $parent = null)
    {
        $this->_value = $value;
        if (null !== $parent) {
            $this->setParent($parent, true);
        }

        return $this;
    }

    /**
     * Set parent node
     *
     * @param \Zend\Server\Reflection\Node $node
     * @param boolean $new Whether or not the child node is newly created
     * and should always be attached
     * @return void
     */
    public function setParent(Node $node, $new = false)
    {
        $this->_parent = $node;

        if ($new) {
            $node->attachChild($this);
            return;
        }
    }

    /**
     * Create and attach a new child node
     *
     * @param mixed $value
     * @access public
     * @return \Zend\Server\Reflection\Node New child node
     */
    public function createChild($value)
    {
        $child = new self($value, $this);

        return $child;
    }

    /**
     * Attach a child node
     *
     * @param \Zend\Server\Reflection\Node $node
     * @return void
     */
    public function attachChild(Node $node)
    {
        $this->_children[] = $node;

        if ($node->getParent() !== $this) {
            $node->setParent($this);
        }
    }

    /**
     * Return an array of all child nodes
     *
     * @return array
     */
    public function getChildren()
    {
        return $this->_children;
    }

    /**
     * Does this node have children?
     *
     * @return boolean
     */
    public function hasChildren()
    {
        return count($this->_children) > 0;
    }

    /**
     * Return the parent node
     *
     * @return null|\Zend\Server\Reflection\Node
     */
    public function getParent()
    {
        return $this->_parent;
    }

    /**
     * Return the node's current value
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->_value;
    }

    /**
     * Set the node value
     *
     * @param mixed $value
     * @return void
     */
    public function setValue($value)
    {
        $this->_value = $value;
    }

    /**
     * Retrieve the bottommost nodes of this node's tree
     *
     * Retrieves the bottommost nodes of the tree by recursively calling
     * getEndPoints() on all children. If a child is null, it returns the parent
     * as an end point.
     *
     * @return array
     */
    public function getEndPoints()
    {
        $endPoints = array();
        if (!$this->hasChildren()) {
            return $endPoints;
        }

        foreach ($this->_children as $child) {
            $value = $child->getValue();

            if (null === $value) {
                $endPoints[] = $this;
            } elseif ((null !== $value)
                && $child->hasChildren())
            {
                $childEndPoints = $child->getEndPoints();
                if (!empty($childEndPoints)) {
                    $endPoints = array_merge($endPoints, $childEndPoints);
                }
            } elseif ((null !== $value) && !$child->hasChildren()) {
                $endPoints[] = $child;
            }
        }

        return $endPoints;
    }
}
