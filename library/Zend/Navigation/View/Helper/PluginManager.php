<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_View
 */

namespace Zend\Navigation\View\Helper;

use Zend\View\Exception;
use Zend\View\HelperPluginManager;

/**
 * Plugin manager implementation for navigation helpers
 *
 * Enforces that helpers retrieved are instances of
 * Navigation\HelperInterface. Additionally, it registers a number of default
 * helpers.
 *
 * @category   Zend
 * @package    Zend_View
 * @subpackage Helper
 */
class PluginManager extends HelperPluginManager
{
    /**
     * Default set of helpers
     *
     * @var array
     */
    protected $invokableClasses = array(
        'breadcrumbs' => 'Zend\Navigation\View\Helper\Breadcrumbs',
        'links'       => 'Zend\Navigation\View\Helper\Links',
        'menu'        => 'Zend\Navigation\View\Helper\Menu',
        'sitemap'     => 'Zend\Navigation\View\Helper\Sitemap',
    );

    /**
     * Validate the plugin
     *
     * Checks that the helper loaded is an instance of AbstractHelper.
     *
     * @param  mixed                              $plugin
     * @return void
     * @throws Exception\InvalidArgumentException if invalid
     */
    public function validatePlugin($plugin)
    {
        if ($plugin instanceof AbstractHelper) {
            // we're okay
            return;
        }

        throw new Exception\InvalidArgumentException(sprintf(
            'Plugin of type %s is invalid; must implement %s\AbstractHelper',
            (is_object($plugin) ? get_class($plugin) : gettype($plugin)),
            __NAMESPACE__
        ));
    }
}
