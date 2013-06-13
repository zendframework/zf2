<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Filter\Compress;

use Zend\Filter\Exception;
use Zend\ServiceManager\AbstractPluginManager;

class CompressionAdapterPluginManager extends AbstractPluginManager
{
    /**
     * Default set of compression adapters
     *
     * @var array
     */
    protected $invokableClasses = array(
        'bz2'    => 'Zend\Filter\Compress\Bz2',
        'gz'     => 'Zend\Filter\Compress\Gz',
        'lzf'    => 'Zend\Filter\Compress\Lzf',
        'rar'    => 'Zend\Filter\Compress\Rar',
        'snappy' => 'Zend\Filter\Compress\Snappy',
        'tar'    => 'Zend\Filter\Compress\Tar',
        'zip'    => 'Zend\Filter\Compress\Zip',
    );

    /**
     * Whether or not to share by default; default to false
     *
     * @var bool
     */
    protected $shareByDefault = false;

    /**
     * {@inheritDoc}
     */
    public function validatePlugin($plugin)
    {
        if ($plugin instanceof CompressionAdapterInterface) {
            return; // we're okay
        }

        throw new Exception\RuntimeException(sprintf(
            'Plugin of type %s is invalid; must implement Zend\Filter\Compress\CompressionAdapterInterface',
            (is_object($plugin) ? get_class($plugin) : gettype($plugin))
        ));
    }
}
