<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\View\Renderer;

class JsonRendererOptions extends AbstractRendererOptions
{
    /**
     * JSONP callback (if set, wraps the return in a function call)
     *
     * @var string
     */
    protected $jsonpCallback;

    /**
     * Whether or not to merge child models with no capture-to value set
     *
     * @var bool
     */
    protected $mergeUnnamedChildren = false;

    /**
     * @var bool Whether or not to render trees of view models
     */
    protected $renderTrees = true;

    /**
     * Set the JSONP callback function name
     *
     * @param  string $callback
     * @return JsonRendererOptions
     */
    public function setJsonpCallback($callback)
    {
        $callback = (string) $callback;
        if (!empty($callback)) {
            $this->jsonpCallback = $callback;
        }

        return $this;
    }

    /**
     * Get the JSONP callback function name
     *
     * @return string
     */
    public function getJsonpCallback()
    {
        return $this->jsonpCallback;
    }

    /**
     * Returns whether or not the jsonpCallback has been set
     *
     * @return bool
     */
    public function hasJsonpCallback()
    {
        return (null !== $this->jsonpCallback);
    }

    /**
     * Set flag indicating whether or not to merge unnamed children
     *
     * @param  bool $mergeUnnamedChildren
     * @return JsonRendererOptions
     */
    public function setMergeUnnamedChildren($mergeUnnamedChildren)
    {
        $this->mergeUnnamedChildren = (bool) $mergeUnnamedChildren;

        return $this;
    }

    /**
     * Should we merge unnamed children?
     *
     * @return bool
     */
    public function canMergeUnnamedChildren()
    {
        return $this->mergeUnnamedChildren;
    }
}
