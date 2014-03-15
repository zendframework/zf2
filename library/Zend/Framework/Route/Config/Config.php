<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Route\Config;

use Zend\Framework\Config\ConfigTrait as ConfigTrait;

class Config
    implements ConfigInterface
{
    /**
     *
     */
    use ConfigTrait;

    /**
     * @return array
     */
    public function aliases()
    {
        return $this->get('plugins');
    }

    /**
     * @return array
     */
    public function defaultParams()
    {
        return $this->get('default_params');
    }

    /**
     * @return array
     */
    public function listeners()
    {
        return $this->get('listeners');
    }

    /**
     * @return array
     */
    public function routes()
    {
        return $this->get('routes');
    }
}
