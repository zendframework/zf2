<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\View\Renderer;

use Zend\Filter\FilterChain;
use Zend\View\Exception;
use Zend\View\Model\ModelInterface;
use Zend\View\Renderer\ConsoleRendererOptions;
use Zend\View\Renderer\RendererInterface as Renderer;
use Zend\View\Resolver\ResolverInterface as Resolver;

/**
 * Abstract class for Zend_View to help enforce private constructs.
 *
 * Note: all private variables in this class are prefixed with "__". This is to
 * mark them as part of the internal implementation, and thus prevent conflict
 * with variables injected into the renderer.
 */
class ConsoleRenderer implements Renderer
{
    /**
     * @var FilterChain
     */
    protected $filterChain;

    /**
     * Renderer options
     *
     * @var ConsoleRendererOptions
     */
    protected $options;

    /**
     * Constructor.
     *
     * @todo handle passing helper manager, options
     * @todo handle passing filter chain, options
     * @todo handle passing variables object, options
     * @todo handle passing resolver object, options
     * @param array $config Configuration key-value pairs.
     */
    public function __construct($config = array())
    {
        $this->init();
    }

    /**
     * Set renderer options
     *
     * @param  array|\Traversable|ConsoleRendererOptions $options
     * @throws Exception\InvalidArgumentException
     * @return ConsoleRenderer
     */
    public function setOptions($options)
    {
        if (!$options instanceof ConsoleRendererOptions) {
            if (is_object($options) && !$options instanceof ConsoleRendererOptions) {
                throw new Exception\InvalidArgumentException(sprintf(
                    'Expected instance of Zend\View\Renderer\ConsoleRendererOptions; '
                    . 'received "%s"', get_class($options))
                );
            }

            $options = new ConsoleRendererOptions($options);
        }

        $this->options = $options;

        return $this;
    }

    /**
     * Get renderer options
     *
     * @return ConsoleRendererOptions
     */
    public function getOptions()
    {
        if (!$this->options) {
            $this->setOptions(new ConsoleRendererOptions());
        }

        return $this->options;
    }

    public function setResolver(Resolver $resolver)
    {
        return $this;
    }

    /**
     * Return the template engine object
     *
     * Returns the object instance, as it is its own template engine
     *
     * @return ConsoleRenderer
     */
    public function getEngine()
    {
        return $this;
    }

    /**
     * Allow custom object initialization when extending Zend_View_Abstract or
     * Zend_View
     *
     * Triggered by {@link __construct() the constructor} as its final action.
     *
     * @return void
     */
    public function init()
    {
    }

    /**
     * Set filter chain
     *
     * @param  FilterChain $filters
     * @return ConsoleRenderer
     */
    public function setFilterChain(FilterChain $filters)
    {
        $this->filterChain = $filters;

        return $this;
    }

    /**
     * Retrieve filter chain for post-filtering script content
     *
     * @return FilterChain
     */
    public function getFilterChain()
    {
        if (null === $this->filterChain) {
            $this->setFilterChain(new FilterChain());
        }

        return $this->filterChain;
    }

    /**
     * Recursively processes all models and returns output.
     *
     * @param  string|ModelInterface   $model  A model instance.
     * @param  null|array|\Traversable $values Values to use when rendering. If none
     *                                         provided, uses those in the composed
     *                                         variables container.
     * @return string Console output.
     */
    public function render($model, $values = null)
    {
        if (!$model instanceof ModelInterface) {
            return '';
        }

        $result = '';
        $options = $model->getOptions();
        foreach ($options as $setting => $value) {
            $method = 'set' . $setting;
            if (method_exists($this, $method)) {
                $this->$method($value);
            }
            unset($method, $setting, $value);
        }
        unset($options);

        $values = $model->getVariables();

        if (isset($values['result'])) {
            // filter and append the result
            $result .= $this->getFilterChain()->filter($values['result']);
        }

        if ($model->hasChildren()) {
            // recursively render all children
            foreach ($model->getChildren() as $child) {
                $result .= $this->render($child, $values);
            }
        }

        return $result;
    }
}
