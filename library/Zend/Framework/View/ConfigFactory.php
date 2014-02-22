<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\View;

use Zend\Framework\Service\RequestInterface as Request;
use Zend\Framework\Service\Factory\Factory;
use Zend\View\Helper as ViewHelper;

class ConfigFactory
    extends Factory
{
    /**
     * @param Request $request
     * @param array $options
     * @return ConfigInterface
     */
    public function __invoke(Request $request, array $options = [])
    {
        return $this->sm->get('Config')->viewConfig();
    }
}
