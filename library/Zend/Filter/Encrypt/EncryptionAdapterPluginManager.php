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
     * Default set of compression adapters
     *
     * @var array
     */
    protected $invokableClasses = array(
        'blockcipher' => 'Zend\Filter\Encrypt\BlockCipher',
        'openssl'     => 'Zend\Filter\Encrypt\Openssl',
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
        if ($plugin instanceof EncryptionAlgorithmInterface) {
            return; // we're okay
        }

        throw new Exception\RuntimeException(sprintf(
            'Plugin of type %s is invalid; must implement Zend\Filter\Encrypt\EncryptionAlgorithmInterface',
            (is_object($plugin) ? get_class($plugin) : gettype($plugin))
        ));
    }
}
