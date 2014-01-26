<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\View\Template\Resolver;

use Zend\Framework\Application\Config\ServicesTrait as Config;
use Zend\Framework\Service\RequestInterface as Request;
use Zend\Framework\Service\Factory\Factory;
use Zend\View\Resolver\TemplateMapResolver;

class MapFactory
    extends Factory
{
    /**
     *
     */
    use Config;

    /**
     * @param Request $request
     * @return TemplateMapResolver
     */
    public function service(Request $request)
    {
        $config = $this->appConfig();
        $map = array();
        if (isset($config['view_manager'])) {
            $config = $config['view_manager'];
            if (is_array($config) && isset($config['template_map'])) {
                $map = $config['template_map'];
            }
        }
        return new TemplateMapResolver($map);
    }
}
