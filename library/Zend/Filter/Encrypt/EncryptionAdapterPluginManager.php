<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Filter\Encrypt;

use Zend\Filter\Exception;
use Zend\ServiceManager\AbstractPluginManager;

class EncryptionAdapterPluginManager extends AbstractPluginManager
{
    /**
     * @var array
     */
    protected $factories = array(
        'blockcipher' => 'Zend\Filter\Factory\BlockCipherAdapterFactory',
        'openssl'     => 'Zend\Filter\Factory\OpensslAdapterFactory',
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
        if ($plugin instanceof EncryptionAdapterInterface) {
            return; // we're okay
        }

        throw new Exception\RuntimeException(sprintf(
            'Plugin of type %s is invalid; must implement Zend\Filter\Encrypt\EncryptionAdapterInterface',
            (is_object($plugin) ? get_class($plugin) : gettype($plugin))
        ));
    }
}
