<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Crypt\Filter\Adapter;

use Zend\Filter\Exception;
use Zend\ServiceManager\AbstractPluginManager;

class EncryptionAdapterPluginManager extends AbstractPluginManager
{
    /**
     * @var array
     */
    protected $invokableClasses = array(
        'Zend\Crypt\Filter\Adapter\BlockCipher' => 'Zend\Crypt\Filter\Adapter\BlockCipher',
        'Zend\Crypt\Filter\Adapter\OpenSsl'     => 'Zend\Crypt\Filter\Adapter\OpenSsl',
    );

    /**
     * @var array
     */
    protected $aliases = array(
        'blockcipher' => 'Zend\Crypt\Filter\Adapter\BlockCipher',
        'openssl'     => 'Zend\Crypt\Filter\Adapter\OpenSsl',
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
            'Plugin of type %s is invalid; must implement Zend\Crypt\Filter\Adapter\EncryptionAdapterInterface',
            (is_object($plugin) ? get_class($plugin) : gettype($plugin))
        ));
    }
}
