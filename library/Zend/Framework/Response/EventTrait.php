<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Response;

use Zend\Framework\Event\EventTrait as Event;

trait EventTrait
{
    /**
     *
     */
    use Event;

    /**
     * @var array
     */
    protected $contentSent = [];

    /**
     * @var array
     */
    protected $headersSent = [];

    /**
     * Set content sent for current response
     *
     * @return self
     */
    public function setContentSent()
    {
        $response = $this->target();
        $this->contentSent[spl_object_hash($response)] = true;
        return $this;
    }

    /**
     * @return bool
     */
    public function contentSent()
    {
        $response = $this->target();
        if (isset($this->contentSent[spl_object_hash($response)])) {
            return true;
        }
        return false;
    }

    /**
     * Set headers sent for current response object
     *
     * @return self
     */
    public function setHeadersSent()
    {
        $response = $this->target();
        $this->headersSent[spl_object_hash($response)] = true;
        return $this;
    }

    /**
     * @return bool
     */
    public function headersSent()
    {
        $response = $this->target();
        if (isset($this->headersSent[spl_object_hash($response)])) {
            return true;
        }
        return false;
    }
}
