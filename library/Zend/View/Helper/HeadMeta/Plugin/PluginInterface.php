<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\View\Helper\HeadMeta\Plugin;

/**
 * Extends API of the HeadMeta view helper, so that some custom invokations
 * of that helper can be handled.
 *
 * @author Nikola Posa <posa.nikola@gmail.com>
 */
interface PluginInterface
{
    /**
     * Overload method access
     *
     * If request can be handled, this method should return array containing
     *  - 'action' - name of the action on container, i.e. "prepend", "append"
     *  - 'type' - meta attribute name, i.e. "name", "property", "itemprop"
     *  - 'typeValue' - value for the 'type' attribute
     *  - 'content' - value for the meta content attribute
     *  - 'modifiers' - meta tag modifiers OPTIONAL
     *  - 'offsetIndex' - required only if 'action' is "offsetSet"
     *
     * ... otherwise, boolean false should be returned.
     *
     * @param  string $method
     * @param  array  $args
     * @return array|false
     */
    public function handle($method, $args);
}
