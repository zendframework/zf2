<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\View\Response;

use Zend\Framework\EventManager\ListenerTrait as ListenerService;
use Zend\Framework\Application\ServiceTrait as Services;

trait ListenerTrait
{
    /**
     *
     */
    use ListenerService, Services;

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
