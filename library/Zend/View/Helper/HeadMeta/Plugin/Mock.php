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
 * @author Nikola Posa <posa.nikola@gmail.com>
 */
class Mock implements PluginInterface
{
    const METHOD_NAME = 'test';

    protected $pluginResult;

    public function __construct(array $pluginResult = array())
    {
        $this->pluginResult = $pluginResult;
    }

    public function handle($method, $args)
    {
        if ($method == self::METHOD_NAME) {
            return $this->pluginResult;
        }

        return false;
    }
}
