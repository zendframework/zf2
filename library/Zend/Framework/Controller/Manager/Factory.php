<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Controller\Manager;

use Zend\Framework\Controller\ServicesConfigTrait as Config;
use Zend\Framework\Service\Factory\Factory as ServiceFactory;
use Zend\Framework\Service\RequestInterface as Request;

class Factory
    extends ServiceFactory
{
    /**
     *
     */
    use Config;

    /**
     * @param Request $request
     * @param array $options
     * @return Manager
     */
    public function __invoke(Request $request, array $options = [])
    {
        return new Manager($this->config());
    }
}
