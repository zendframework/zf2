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
 * Plugin manager default configuration
 */
class ValidatorPluginManagerConfig extends Config
{
    /**
     * {@inheritDoc}
     */
    protected $config = [
        'use_canonical_names' => true,
        'aliases' => [
            'count'            => 'filecount',
            'crc32'            => 'filecrc32',
            'excludeextension' => 'fileexcludeextension',
            'excludemimetype'  => 'fileexcludemimetype',
            'exists'           => 'fileexists',
            'extension'        => 'fileextension',
            'filessize'        => 'filefilessize',
            'hash'             => 'filehash',
            'imagesize'        => 'fileimagesize',
            'iscompressed'     => 'fileiscompressed',
            'isimage'          => 'fileisimage',
            'md5'              => 'filemd5',
            'mimetype'         => 'filemimetype',
            'notexists'        => 'filenotexists',
            'sha1'             => 'filesha1',
            'size'             => 'filesize',
            'upload'           => 'fileupload',
            'wordcount'        => 'filewordcount',
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