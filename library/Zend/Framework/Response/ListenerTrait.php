<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Response;

use Zend\Framework\Event\ListenerTrait as Listener;

trait ListenerTrait
{
    /**
     *
     */
    use Listener;

    /**
     * Placeholders that may hold content
     *
     * @var array
     */
    protected $contentPlaceholders = array('article', 'content');

    /**
     * Set list of possible content placeholders
     *
     * @param  array $contentPlaceholders
     * @return Listener
     */
    public function setContentPlaceholders(array $contentPlaceholders)
    {
        $this->contentPlaceholders = $contentPlaceholders;
        return $this;
    }

    /**
     * Get list of possible content placeholders
     *
     * @return array
     */
    public function getContentPlaceholders()
    {
        return $this->contentPlaceholders;
    }
}
