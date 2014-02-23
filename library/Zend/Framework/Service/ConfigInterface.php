<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Service;

use Zend\Framework\Config\ConfigInterface as Serializable;

interface ConfigInterface
    extends Serializable
{
    /**
     * @param string $name
     * @param callable $delegate
     * @return self
     */
    public function assign($name, $delegate);

    /**
     * @param $name
     * @return mixed
     */
    public function assigned($name);

    /**
     * @param $name
     * @return self
     */
    public function initialized($name);

    /**
     * @param $name
     * @return bool
     */
    public function initializing($name);
}
