<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Filter\Encrypt;

use Zend\Filter\Compress\CompressionAdapterInterface;
use Zend\Filter\Compress\CompressionAdapterPluginManager;
use Zend\Filter\Exception;
use Zend\Stdlib\AbstractOptions;

abstract class AbstractEncryptionAdapter extends AbstractOptions implements EncryptionAdapterInterface
{
    /**
     * @var CompressionAdapterPluginManager
     */
    protected $compressionAdapterPluginManager;

    /**
     * @var CompressionAdapterInterface
     */
    protected $compression;

    /**
     * @param CompressionAdapterPluginManager $compressionAdapterPluginManager
     * @param array|\Traversable|null          $options
     */
    public function __construct(CompressionAdapterPluginManager $compressionAdapterPluginManager, $options = null)
    {
        $this->compressionAdapterPluginManager = $compressionAdapterPluginManager;
        parent::__construct($options);
    }

    /**
     * Sets a internal compression for values to encrypt
     *
     * @param  string|array $compression
     * @throws Exception\RuntimeException
     * @return void
     */
    public function setCompression($compression)
    {
        if (!$this->compressionAdapterPluginManager->has($compression)) {
            throw new Exception\RuntimeException(sprintf(
                'The given compression ("%s") algorithm is not known',
                $compression
            ));
        }

        $this->compression = $this->compressionAdapterPluginManager->get($compression);
    }

    /**
     * Returns the compression adapter
     *
     * @return CompressionAdapterInterface
     */
    public function getCompression()
    {
        return $this->compression;
    }
}
