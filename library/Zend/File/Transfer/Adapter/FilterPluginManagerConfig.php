<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\File\Transfer\Adapter;

use Zend\ServiceManager\Config;
use Zend\Stdlib\ArrayUtils;

/**
 * Default configuration
 */
class FilterPluginManagerConfig extends Config
{
    /**
     * Default set of filters
     *
     * @var array
     */
    protected $config = [
        'aliases' => [
            //'decrypt'   => 'filedecrypt',
            'encrypt'   => 'fileencrypt',
            'lowercase' => 'filelowercase',
            'rename'    => 'filerename',
            'uppercase' => 'fileuppercase',
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
