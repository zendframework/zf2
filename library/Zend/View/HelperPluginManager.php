<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_View
 */

namespace Zend\View;

use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\ConfigurationInterface;

/**
 * Plugin manager implementation for view helpers
 *
 * Enforces that heleprs retrieved are instances of
 * Helper\HelperInterface. Additionally, it registers a number of default
 * helpers.
 *
 * @category   Zend
 * @package    Zend_View
 */
class HelperPluginManager extends AbstractPluginManager
{
    /**
     * Default set of helpers
     *
     * @var array
     */
    protected $invokableClasses = array(
        // basePath, doctype, and url are set up as factories in the ViewHelperManagerFactory.
        // base{ath and url are not very useful without their factories, however the doctype
        // helper works fine as an invokable. The factory for doctype simply checks for the
        // config value from the merged config.
        'doctype'             => 'Zend\View\Helper\Doctype', // overridden by a factory in ViewHelperManagerFactory
        'basePath'            => 'Zend\View\Helper\BasePath',
        'url'                 => 'Zend\View\Helper\Url',
        'cycle'               => 'Zend\View\Helper\Cycle',
        'declareVars'         => 'Zend\View\Helper\DeclareVars',
        'escapeHtml'          => 'Zend\View\Helper\EscapeHtml',
        'escapeHtmlAttr'      => 'Zend\View\Helper\EscapeHtmlAttr',
        'escapeJs'            => 'Zend\View\Helper\EscapeJs',
        'escapeCss'           => 'Zend\View\Helper\EscapeCss',
        'escapeUrl'           => 'Zend\View\Helper\EscapeUrl',
        'gravatar'            => 'Zend\View\Helper\Gravatar',
        'headLink'            => 'Zend\View\Helper\HeadLink',
        'headMeta'            => 'Zend\View\Helper\HeadMeta',
        'headScript'          => 'Zend\View\Helper\HeadScript',
        'headStyle'           => 'Zend\View\Helper\HeadStyle',
        'headTitle'           => 'Zend\View\Helper\HeadTitle',
        'htmlFlash'           => 'Zend\View\Helper\HtmlFlash',
        'htmlList'            => 'Zend\View\Helper\HtmlList',
        'htmlObject'          => 'Zend\View\Helper\HtmlObject',
        'htmlPage'            => 'Zend\View\Helper\HtmlPage',
        'htmlQuicktime'       => 'Zend\View\Helper\HtmlQuicktime',
        'inlineScript'        => 'Zend\View\Helper\InlineScript',
        'json'                => 'Zend\View\Helper\Json',
        'layout'              => 'Zend\View\Helper\Layout',
        'paginationControl'   => 'Zend\View\Helper\PaginationControl',
        'partialLoop'         => 'Zend\View\Helper\PartialLoop',
        'partial'             => 'Zend\View\Helper\Partial',
        'placeholder'         => 'Zend\View\Helper\Placeholder',
        'renderChildModel'    => 'Zend\View\Helper\RenderChildModel',
        'renderToPlaceholder' => 'Zend\View\Helper\RenderToPlaceholder',
        'serverUrl'           => 'Zend\View\Helper\ServerUrl',
        'viewModel'           => 'Zend\View\Helper\ViewModel',
    );

    /**
     * @var Renderer\RendererInterface
     */
    protected $renderer;

    /**
     * Constructor
     *
     * After invoking parent constructor, add an initializer to inject the
     * attached renderer, if any, to the currently requested helper.
     *
     * @param  null|ConfigurationInterface $configuration
     * @return void
     */
    public function __construct(ConfigurationInterface $configuration = null)
    {
        parent::__construct($configuration);
        $this->addInitializer(array($this, 'injectRenderer'));
    }

    /**
     * Set renderer
     *
     * @param  Renderer\RendererInterface $renderer
     * @return HelperPluginManager
     */
    public function setRenderer(Renderer\RendererInterface $renderer)
    {
        $this->renderer = $renderer;
        return $this;
    }

    /**
     * Retrieve renderer instance
     *
     * @return null|Renderer\RendererInterface
     */
    public function getRenderer()
    {
        return $this->renderer;
    }

    /**
     * Inject a helper instance with the registered renderer
     *
     * @param  Helper\HelperInterface $helper
     * @return void
     */
    public function injectRenderer($helper)
    {
        $renderer = $this->getRenderer();
        if (null === $renderer) {
            return;
        }
        $helper->setView($renderer);
    }

    /**
     * Validate the plugin
     *
     * Checks that the helper loaded is an instance of Helper\HelperInterface.
     *
     * @param  mixed $plugin
     * @return void
     * @throws Exception\InvalidHelperException if invalid
     */
    public function validatePlugin($plugin)
    {
        if ($plugin instanceof Helper\HelperInterface) {
            // we're okay
            return;
        }

        throw new Exception\InvalidHelperException(sprintf(
            'Plugin of type %s is invalid; must implement %s\Helper\HelperInterface',
            (is_object($plugin) ? get_class($plugin) : gettype($plugin)),
            __NAMESPACE__
        ));
    }
}
