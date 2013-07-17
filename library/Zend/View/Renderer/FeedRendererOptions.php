<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\View\Renderer;

use Zend\View\Exception;

class FeedRendererOptions extends AbstractRendererOptions
{
    /**
     * @var string 'rss' or 'atom'; defaults to 'rss'
     */
    protected $feedType = 'rss';

    /**
     * @var bool Whether or not to render trees of view models
     */
    protected $renderTrees = false;

    /**
     * Set feed type ('rss' or 'atom')
     *
     * @param  string $feedType
     * @throws Exception\InvalidArgumentException
     * @return FeedRendererOptions
     */
    public function setFeedType($feedType)
    {
        $feedType = strtolower($feedType);
        if (!in_array($feedType, array('rss', 'atom'))) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s expects a string of either "rss" or "atom"',
                __METHOD__
            ));
        }

        $this->feedType = $feedType;

        return $this;
    }

    /**
     * Get feed type
     *
     * @return string
     */
    public function getFeedType()
    {
        return $this->feedType;
    }
}
