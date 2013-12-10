<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Mvc\Controller;


use Zend\ServiceManager\Config;
use Zend\Stdlib\ArrayUtils;

/**
 * Plugin manager default configuration
 */
class PluginManagerConfig extends Config
{
    /**
     * {@inheritDoc}
     */
    protected $config = [
        'use_canonical_names' => true,
        'factories' => [
            'forward'  => 'Zend\Mvc\Controller\Plugin\Service\ForwardFactory',
            'identity' => 'Zend\Mvc\Controller\Plugin\Service\IdentityFactory',
        ],
        'invokables' => [
            'acceptableviewmodelselector' => 'Zend\Mvc\Controller\Plugin\AcceptableViewModelSelector',
            'filepostredirectget'         => 'Zend\Mvc\Controller\Plugin\FilePostRedirectGet',
            'flashmessenger'              => 'Zend\Mvc\Controller\Plugin\FlashMessenger',
            'layout'                      => 'Zend\Mvc\Controller\Plugin\Layout',
            'params'                      => 'Zend\Mvc\Controller\Plugin\Params',
            'postredirectget'             => 'Zend\Mvc\Controller\Plugin\PostRedirectGet',
            'redirect'                    => 'Zend\Mvc\Controller\Plugin\Redirect',
            'url'                         => 'Zend\Mvc\Controller\Plugin\Url',
        ],
        'aliases' => [
            'prg'     => 'postredirectget',
            'fileprg' => 'filepostredirectget',
        ],
    ];

    /**
     * {@inheritDoc}
     */
    public function __construct($config = array())
    {
        parent::__construct(ArrayUtils::merge($this->config, $config));
    }
} 