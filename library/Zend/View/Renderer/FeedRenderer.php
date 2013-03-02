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
use Zend\View\Model\FeedModel;
use Zend\View\Renderer\FeedRendererOptions;
use Zend\View\Renderer\RendererInterface as Renderer;
use Zend\View\Resolver\ResolverInterface as Resolver;

/**
 * Interface class for Zend_View compatible template engine implementations
 */
class FeedRenderer implements Renderer
{
    /**
     * Renderer options
     *
     * @var FeedRendererOptions
     */
    protected $options;

    /**
     * @var Resolver
     */
    protected $resolver;

    /**
     * Return the template engine object, if any
     *
     * If using a third-party template engine, such as Smarty, patTemplate,
     * phplib, etc, return the template engine object. Useful for calling
     * methods on these objects, such as for setting filters, modifiers, etc.
     *
     * @return FeedRenderer
     */
    public function getEngine()
    {
        return $this;
    }

    /**
     * Set renderer options
     *
     * @param  array|\Traversable|FeedRendererOptions $options
     * @throws Exception\InvalidArgumentException
     * @return FeedRenderer
     */
    public function setOptions($options)
    {
        if (!$options instanceof FeedRendererOptions) {
            if (is_object($options) && !$options instanceof FeedRendererOptions) {
                throw new Exception\InvalidArgumentException(sprintf(
                        'Expected instance of Zend\View\Renderer\FeedRendererOptions; '
                        . 'received "%s"', get_class($options))
                );
            }

            $options = new FeedRendererOptions($options);
        }

        $this->options = $options;

        return $this;
    }

    /**
     * Get renderer options
     *
     * @return FeedRendererOptions
     */
    public function getOptions()
    {
        if (!$this->options) {
            $this->setOptions(new FeedRendererOptions());
        }

        return $this->options;
    }

    /**
     * Set the resolver used to map a template name to a resource the renderer may consume.
     *
     * @todo   Determine use case for resolvers for feeds
     * @param  Resolver $resolver
     * @return FeedRenderer
     */
    public function setResolver(Resolver $resolver)
    {
        $this->resolver = $resolver;

        return $this;
    }

    /**
     * Renders values as JSON
     *
     * @todo   Determine what use case exists for accepting only $nameOrModel
     * @param  string|FeedModel $nameOrModel The script/resource process, or a view model
     * @param  null|array|\ArrayAccess $values Values to use during rendering
     * @throws Exception\InvalidArgumentException
     * @return string The script output.
     */
    public function render($nameOrModel, $values = null)
    {
        if(!$nameOrModel instanceof FeedModel) {
            if (is_string($nameOrModel)) {
                // Use case 1: string $nameOrModel + array|Traversable|Feed $values
                $nameOrModel = new FeedModel($values, array('feed_type' => $nameOrModel));
            } else {
                // Use case 2: failure
                throw new Exception\InvalidArgumentException(sprintf(
                    '%s expects a "Zend\View\Model\FeedModel" or a string feed type '
                    . 'as the first argument; received "%s"',
                    __METHOD__,
                    (is_object($nameOrModel) ? get_class($nameOrModel) : gettype($nameOrModel))
                ));
            }
        }

        // Get feed and type
        $feed = $nameOrModel->getFeed();
        $type = $nameOrModel->getOptions()->getFeedType();
        if (!$type) {
            $type = $this->getOptions()->getFeedType();
        } else {
            $this->getOptions()->setFeedType($type);
        }

        // Render feed
        return $feed->export($type);
    }
}
