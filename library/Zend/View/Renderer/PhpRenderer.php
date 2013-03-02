<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\View\Renderer;

use ArrayAccess;
use Traversable;
use Zend\Filter\FilterChain;
use Zend\View\Exception;
use Zend\View\HelperPluginManager;
use Zend\View\Helper\AbstractHelper;
use Zend\View\Model\ModelInterface as Model;
use Zend\View\Renderer\RendererInterface as Renderer;
use Zend\View\Renderer\PhpRendererOptions;
use Zend\View\Resolver\ResolverInterface as Resolver;
use Zend\View\Resolver\TemplatePathStack;
use Zend\View\Variables;

/**
 * Abstract class for Zend_View to help enforce protected constructs.
 *
 * Note: all protected variables in this class are prefixed with "__". This is to
 * mark them as part of the internal implementation, and thus prevent conflict
 * with variables injected into the renderer.
 */
class PhpRenderer implements Renderer
{
    /**
     * @var string Rendered content
     */
    private $__content = '';

    /**
     * Script file name to execute
     *
     * @var string
     */
    private $__file;

    /**
     * @var FilterChain
     */
    private $__filterChain;

    /**
     * Helper plugin manager
     *
     * @var HelperPluginManager
     */
    private $__helpers;

    /**
     * Renderer options
     *
     * @var PhpRendererOptions
     */
    private $__options;

    /**
     * @var array Cache for the plugin call
     */
    private $__pluginCache = array();

    /**
     * Template resolver
     *
     * @var Resolver
     */
    private $__resolver;

    /**
     * Template being rendered
     *
     * @var null|string
     */
    private $__template = null;

    /**
     * Queue of templates to render
     * @var array
     */
    private $__templates = array();

    /**
     * @var ArrayAccess|array ArrayAccess or associative array representing available variables
     */
    private $__vars;

    /**
     * @var array Temporary variable stack; used when variables passed to render()
     */
    private $__varsCache = array();

    /**
     * Constructor.
     *
     * @todo handle passing helper plugin manager, options
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
     * Return the template engine object
     *
     * Returns the object instance, as it is its own template engine
     *
     * @return PhpRenderer
     */
    public function getEngine()
    {
        return $this;
    }

    /**
     * Set renderer options
     *
     * @param  array|\Traversable|PhpRendererOptions $options
     * @throws Exception\InvalidArgumentException
     * @return PhpRenderer
     */
    public function setOptions($options)
    {
        if (!$options instanceof PhpRendererOptions) {
            if (is_object($options) && !$options instanceof PhpRendererOptions) {
                throw new Exception\InvalidArgumentException(sprintf(
                        'Expected instance of Zend\View\Renderer\PhpRendererOptions; '
                        . 'received "%s"', get_class($options))
                );
            }

            $options = new PhpRendererOptions($options);
        }

        $this->__options = $options;

        return $this;
    }

    /**
     * Get renderer options
     *
     * @return PhpRendererOptions
     */
    public function getOptions()
    {
        if (!$this->__options) {
            $this->setOptions(new PhpRendererOptions());
        }

        return $this->__options;
    }

    /**
     * Set script resolver
     *
     * @param  Resolver $resolver
     * @throws Exception\InvalidArgumentException
     * @return PhpRenderer
     */
    public function setResolver(Resolver $resolver)
    {
        $this->__resolver = $resolver;

        return $this;
    }

    /**
     * Retrieve template name or template resolver
     *
     * @param  null|string $name
     * @return string|Resolver
     */
    public function resolver($name = null)
    {
        if (null === $this->__resolver) {
            $this->setResolver(new TemplatePathStack());
        }

        if (null !== $name) {
            return $this->__resolver->resolve($name, $this);
        }

        return $this->__resolver;
    }

    /**
     * Overloading: proxy to Variables container
     *
     * @param  string $name
     * @return mixed
     */
    public function __get($name)
    {
        $vars = $this->vars();

        return $vars[$name];
    }

    /**
     * Overloading: proxy to Variables container
     *
     * @param  string $name
     * @param  mixed $value
     * @return void
     */
    public function __set($name, $value)
    {
        $vars = $this->vars();
        $vars[$name] = $value;
    }

    /**
     * Overloading: proxy to Variables container
     *
     * @param  string $name
     * @return bool
     */
    public function __isset($name)
    {
        $vars = $this->vars();

        return isset($vars[$name]);
    }

    /**
     * Overloading: proxy to Variables container
     *
     * @param  string $name
     * @return void
     */
    public function __unset($name)
    {
        $vars = $this->vars();
        if (!isset($vars[$name])) {
            return;
        }
        unset($vars[$name]);
    }

    /**
     * Set variable storage
     *
     * Expects either an array, or an object implementing ArrayAccess.
     *
     * @param  array|ArrayAccess $variables
     * @throws Exception\InvalidArgumentException
     * @return PhpRenderer
     */
    public function setVars($variables)
    {
        if (!is_array($variables) && !$variables instanceof ArrayAccess) {
            throw new Exception\InvalidArgumentException(sprintf(
                'Expected array or ArrayAccess object; received "%s"',
                (is_object($variables) ? get_class($variables) : gettype($variables))
            ));
        }

        // Enforce a Variables container
        if (!$variables instanceof Variables) {
            $variablesAsArray = array();
            foreach ($variables as $key => $value) {
                $variablesAsArray[$key] = $value;
            }
            $variables = new Variables($variablesAsArray);
        }

        $this->__vars = $variables;

        return $this;
    }

    /**
     * Get a single variable, or all variables
     *
     * @param  mixed $key
     * @return mixed
     */
    public function vars($key = null)
    {
        if (null === $this->__vars) {
            $this->setVars(new Variables());
        }

        if (null === $key) {
            return $this->__vars;
        }

        return $this->__vars[$key];
    }

    /**
     * Get a single variable
     *
     * @param  mixed $key
     * @return mixed
     */
    public function get($key)
    {
        if (null === $this->__vars) {
            $this->setVars(new Variables());
        }

        return $this->__vars[$key];
    }

    /**
     * Set helper plugin manager instance
     *
     * @param  string|HelperPluginManager $helpers
     * @throws Exception\InvalidArgumentException
     * @return PhpRenderer
     */
    public function setHelperPluginManager($helpers)
    {
        if (is_string($helpers)) {
            if (!class_exists($helpers)) {
                throw new Exception\InvalidArgumentException(sprintf(
                    'Invalid helper helpers class provided (%s)',
                    $helpers
                ));
            }
            $helpers = new $helpers();
        }
        if (!$helpers instanceof HelperPluginManager) {
            throw new Exception\InvalidArgumentException(sprintf(
                'Helper helpers must extend Zend\View\HelperPluginManager; got type "%s" instead',
                (is_object($helpers) ? get_class($helpers) : gettype($helpers))
            ));
        }
        $helpers->setRenderer($this);
        $this->__helpers = $helpers;

        return $this;
    }

    /**
     * Get helper plugin manager instance
     *
     * @return HelperPluginManager
     */
    public function getHelperPluginManager()
    {
        if (null === $this->__helpers) {
            $this->setHelperPluginManager(new HelperPluginManager());
        }

        return $this->__helpers;
    }

    /**
     * Get plugin instance
     *
     * @param  string     $name Name of plugin to return
     * @param  null|array $options Options to pass to plugin constructor (if not already instantiated)
     * @return AbstractHelper
     */
    public function plugin($name, array $options = null)
    {
        return $this->getHelperPluginManager()->get($name, $options);
    }

    /**
     * Overloading: proxy to helpers
     *
     * Proxies to the attached plugin manager to retrieve, return, and potentially
     * execute helpers.
     *
     * * If the helper does not define __invoke, it will be returned
     * * If the helper does define __invoke, it will be called as a functor
     *
     * @param  string $method
     * @param  array $argv
     * @return mixed
     */
    public function __call($method, $argv)
    {
        if (!isset($this->__pluginCache[$method])) {
            $this->__pluginCache[$method] = $this->plugin($method);
        }
        if (is_callable($this->__pluginCache[$method])) {
            return call_user_func_array($this->__pluginCache[$method], $argv);
        }

        return $this->__pluginCache[$method];
    }

    /**
     * Set filter chain
     *
     * @param  FilterChain $filters
     * @return PhpRenderer
     */
    public function setFilterChain(FilterChain $filters)
    {
        $this->__filterChain = $filters;

        return $this;
    }

    /**
     * Retrieve filter chain for post-filtering script content
     *
     * @return FilterChain
     */
    public function getFilterChain()
    {
        if (null === $this->__filterChain) {
            $this->setFilterChain(new FilterChain());
        }

        return $this->__filterChain;
    }

    /**
     * Processes a view script and returns the output.
     *
     * @param  string|Model $nameOrModel Either the template to use, or a
     *                                   ViewModel. The ViewModel must have the
     *                                   template as an option in order to be
     *                                   valid.
     * @param  null|array|Traversable $values Values to use when rendering. If none
     *                                        provided, uses those in the composed
     *                                        variables container.
     * @throws Exception\RuntimeException
     * @throws Exception\DomainException
     * @throws \Exception
     * @return string The script output.
     */
    public function render($nameOrModel, $values = null)
    {
        if ($nameOrModel instanceof Model) {
            $model       = $nameOrModel;
            $nameOrModel = $model->getOptions()->getTemplate();
            if (empty($nameOrModel)) {
                throw new Exception\DomainException(sprintf(
                    '%s: received View Model argument, but template is empty',
                    __METHOD__
                ));
            }
            $options = $model->getOptions();
            foreach ($options as $setting => $value) {
                $method = 'set' . $setting;
                if (method_exists($this, $method)) {
                    $this->$method($value);
                }
                unset($method, $setting, $value);
            }
            unset($options);

            // Give view model awareness via ViewModel helper
            $helper = $this->plugin('view_model');
            $helper->setCurrent($model);

            $values = $model->getVariables();
            unset($model);
        }

        // find the script file name using the parent protected method
        $this->addTemplate($nameOrModel);
        unset($nameOrModel); // remove $name from local scope

        $this->__varsCache[] = $this->vars();

        if (null !== $values) {
            $this->setVars($values);
        }
        unset($values);

        // extract all assigned vars (pre-escaped), but not 'this'.
        // assigns to a double-underscored variable, to prevent naming collisions
        $__vars = $this->vars()->getArrayCopy();
        if (array_key_exists('this', $__vars)) {
            unset($__vars['this']);
        }
        extract($__vars);
        unset($__vars); // remove $__vars from local scope

        while ($this->__template = array_pop($this->__templates)) {
            $this->__file = $this->resolver($this->__template);

            if (!$this->__file) {
                throw new Exception\RuntimeException(sprintf(
                    '%s: Unable to render template "%s"; resolver could not resolve to a file',
                    __METHOD__,
                    $this->__template
                ));
            }

            try {
                ob_start();
                include $this->__file;
                $this->__content = ob_get_clean();
            } catch (\Exception $ex) {
                ob_end_clean();
                throw $ex;
            }
        }

        $this->setVars(array_pop($this->__varsCache));

        return $this->getFilterChain()->filter($this->__content); // filter output
    }

    /**
     * Add a template to the stack
     *
     * @param  string $template
     * @return PhpRenderer
     */
    public function addTemplate($template)
    {
        $this->__templates[] = $template;

        return $this;
    }

    /**
     * Make sure View variables are cloned when the view is cloned.
     *
     * @return PhpRenderer
     */
    public function __clone()
    {
        $this->__vars = clone $this->vars();
    }
}
