<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Service\Factory;

use Zend\Framework\Service\RequestInterface as Request;

interface FactoryInterface
{
    /**
     * @param Request $request
     * @param array $options
     * @return mixed
     */
    public function __invoke(Request $request, array $options = []);
}
