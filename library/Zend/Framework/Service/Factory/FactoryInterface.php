<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Service\Factory;

use Zend\Framework\Application\Manager\ConfigInterface as ApplicationConfig;
use Zend\Framework\Service\RequestInterface as Request;

interface FactoryInterface
{
    /**
     * @return ApplicationConfig
     */
    public function config();

    /**
     * @param string $name
     * @param mixed $options
     * @return null|object
     */
    public function create($name, $options = null);

    /**
     * @param string $name
     * @param mixed $options
     * @param bool $shared
     * @return null|object
     */
    public function get($name, $options = null, $shared = true);

    /**
     * @param string $name
     * @return bool
     */
    public function has($name);

    /**
     * @param Request $request
     * @param array $options
     * @return mixed|void
     */
    public function __invoke(Request $request, array $options = []);
}
