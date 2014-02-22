<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Application\Config;

use Zend\Framework\Service\RequestInterface as Request;
use Zend\Framework\Service\Factory\Factory as ServiceFactory;

/**
 * Class Factory
 *
 * File required for static serialization
 *
 * @package Zend\Framework\Application\Config
 */
class Factory
    extends ServiceFactory
{
    /**
     * @param Config $config
     * @return Config
     */
    public static function factory(Config $config)
    {
        $config->services()->add('Config', $config);
        return $config;
    }

    /**
     * @param Request $request
     * @param array $config
     * @return array|mixed|void
     */
    public function __invoke(Request $request, array $config = [])
    {
        return require 'config/application.config.php';
    }
}
