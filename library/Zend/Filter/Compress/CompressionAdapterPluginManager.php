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
        'Zend\Filter\Compress\Bz2Adapter'    => 'Zend\Filter\Compress\Bz2Adapter',
        'Zend\Filter\Compress\GzAdapter'     => 'Zend\Filter\Compress\GzAdapter',
        'Zend\Filter\Compress\LzfAdapter'    => 'Zend\Filter\Compress\LzfAdapter',
        'Zend\Filter\Compress\RarAdapter'    => 'Zend\Filter\Compress\RarAdapter',
        'Zend\Filter\Compress\SnappyAdapter' => 'Zend\Filter\Compress\SnappyAdapter',
        'Zend\Filter\Compress\TarAdapter'    => 'Zend\Filter\Compress\TarAdapter',
        'Zend\Filter\Compress\ZipAdapter'    => 'Zend\Filter\Compress\ZipAdapter',
    );

    /**
     * List of aliases
     *
     * @var array
     */
    protected $aliases = array(
        'bz2'    => 'Zend\Filter\Compress\Bz2Adapter',
        'gz'     => 'Zend\Filter\Compress\GzAdapter',
        'lzf'    => 'Zend\Filter\Compress\LzfAdapter',
        'rar'    => 'Zend\Filter\Compress\RarAdapter',
        'snappy' => 'Zend\Filter\Compress\SnappyAdapter',
        'tar'    => 'Zend\Filter\Compress\TarAdapter',
        'zip'    => 'Zend\Filter\Compress\ZipAdapter',
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
