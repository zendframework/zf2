<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\View\Model;

class JsonModelOptions extends AbstractModelOptions
{
    /**
     * JSON probably won't need to be captured into a parent container
     * by default
     *
     * @var string
     */
    protected $captureTo = null;

    /**
     * JSONP callback (if set, wraps the return in a function call)
     *
     * @var string
     */
    protected $jsonpCallback = null;

    /**
     * JSON is usually terminal
     *
     * @var bool
     */
    protected $terminal = true;

    /**
     * Set the JSONP callback function name
     *
     * @param  string $callback
     * @return JsonModelOptions
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
}
