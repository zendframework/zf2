<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\File\Transfer\Adapter;

use Zend\ServiceManager\ConfigInterface;
use Zend\ServiceManager\Zf2Compat\ServiceNameNormalizerAbstractFactory;
use Zend\Validator\ValidatorPluginManager as BaseManager;

class ValidatorPluginManager extends BaseManager
{
    /**
     * {@inheritDoc}
     */
    public function __construct(ConfigInterface $configuration = null)
    {
        parent::__construct($configuration ?: new ValidatorPluginManagerConfig());

        $this->addAbstractFactory(new ServiceNameNormalizerAbstractFactory($this), false);
    }
}
