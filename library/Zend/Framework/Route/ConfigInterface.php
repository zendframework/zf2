<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Route;

use Zend\Framework\Config\ConfigInterface as Serializable;

interface ConfigInterface
    extends Serializable
{

    /**
     * @return array
     */
    public function defaultParams();

    /**
     * @return array
     */
    public function plugins();

    /**
     * @return string
     */
    public function routeClass();

    /**
     * @return array
     */
    public function routes();
}
