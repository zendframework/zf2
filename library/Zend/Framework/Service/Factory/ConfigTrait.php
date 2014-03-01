<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Framework\Service\Factory;

use Zend\Framework\Application\Config\ConfigInterface;
use Zend\Framework\Service\ManagerInterface;

trait ConfigTrait
{
    /**
     * @var ManagerInterface
     */
    protected $sm;

    /**
     * @return ConfigInterface
     */
    public function config()
    {
        return $this->sm->config();
    }
}