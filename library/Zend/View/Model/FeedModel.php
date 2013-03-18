<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\View\Model;

use Traversable;
use Zend\Feed\Writer\Feed;
use Zend\Feed\Writer\FeedFactory;
use Zend\View\Exception;
use Zend\View\Variables;

/**
 * Marker view model for indicating feed data.
 */
class FeedModel extends AbstractModel
{
    /**
     * The feed object
     *
     * @var Feed
     */
    protected $feed;

    /**
     * Model options
     *
     * @var FeedModelOptions
     */
    protected $options;

    /**
     * Constructor
     *
     * @param  null|array|Traversable $variables
     * @param  null|array|Traversable|FeedModelOptions $options
     */
    public function __construct($variables = null, $options = null)
    {
        if (null === $variables) {
            $variables = new Variables();
        }

        // Initializing the variables container
        $this->setVariables($variables, true);

        if (null !== $options) {
            $this->setOptions($options);
        }
    }

    /**
     * Set model options
     *
     * @param  array|\Traversable|FeedModelOptions $options
     * @throws Exception\InvalidArgumentException
     * @return FeedModel
     */
    public function setOptions($options)
    {
        if (!$options instanceof FeedModelOptions) {
            if (is_object($options) && !$options instanceof Traversable) {
                throw new Exception\InvalidArgumentException(sprintf(
                    'Expected instance of Zend\View\Model\FeedModelOptions; '
                    . 'received "%s"', get_class($options))
                );
            }

            $options = new FeedModelOptions($options);
        }

        $this->options = $options;

        return $this;
    }

    /**
     * Get model options
     *
     * @return FeedModelOptions
     */
    public function getOptions()
    {
        if (!$this->options) {
            $this->setOptions(new FeedModelOptions());
        }

        return $this->options;
    }

    /**
     * Set the feed object
     *
     * @param  Feed $feed
     * @return FeedModel
     */
    public function setFeed(Feed $feed)
    {
        $this->feed = $feed;

        return $this;
    }

    /**
     * Get the feed object
     *
     * @return Feed
     */
    public function getFeed()
    {
        if (!$this->feed instanceof Feed) {
            $feed = FeedFactory::factory($this->getVariables());
            $this->setFeed($feed);
        }

        return $this->feed;
    }
}
