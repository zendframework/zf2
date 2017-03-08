<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_View
 */

namespace Zend\View\Helper;

use Zend\View\Exception;
use Zend\View\Helper\AbstractHelper;

/**
 * Helper for rendering asset urls, e.g. for assets located at a CDN.
 *
 * @package    Zend_View
 * @subpackage Helper
 */
class AssetUrl extends AbstractHelper
{

    /**
     * A map of registered asset urls.
     *
     * @var array
     */
    protected $assetUrls;

    /**
     * The default key.
     *
     * @var string
     */
    protected $defaultKey;

    /**
     * Constructs instance.
     *
     * @param  array $options
     * @throws Exception\InvalidArgumentException
     */
    public function __construct($options = array())
    {
        if (!is_array($options)) {
            throw new Exception\InvalidArgumentException(sprintf(
                'Parameter "%s" needs to be specified as %s.',
                'options',
                'an array'
            ));
        }

        if (isset($options['default_key'])) {
            $this->setDefaultKey($options['default_key']);
        }

        if (isset($options['asset_urls'])) {
            $this->setAssetUrls($options['asset_urls']);
        }
    }

    /**
     * Invokes the helper.
     *
     * @param  string $file
     * @param  string $key
     * @return string
     * @throws Exception\InvalidArgumentException
     * @throws Exception\BadMethodCallException
     */
    public function __invoke($file, $key = null)
    {
        if (!is_string($file)) {
            throw new Exception\InvalidArgumentException(sprintf(
                'Parameter "%s" needs to be specified as %s.',
                'file',
                'a string'
            ));
        }

        if (null !== $key) {

            if (!is_string($key)) {
                throw new Exception\InvalidArgumentException(sprintf(
                    'Parameter "%s" needs to be specified as %s.',
                    'key',
                    'a string'
                ));
            }

            if (!isset($this->assetUrls[$key])) {
                throw new Exception\BadMethodCallException(sprintf(
                    'An asset url has not been registered for the key "%s".',
                    $key
                ));
            }

        }

        if (null === $key) {
            $key = $this->getDefaultKey();
        }

        if (null === $key) {
            return $file;
        }

        $url = $file;

        if ('' != $this->assetUrls[$key]['prefix']) {

            $url = sprintf(
                '%s/%s',
                $this->assetUrls[$key]['prefix'],
                ltrim(
                    $url,
                    '/'
                )
            );
        }

        if ('' != $this->assetUrls[$key]['suffix']) {
            if (false === strpos($url, '?')) {
                $url = sprintf(
                    '%s?%s',
                    $url,
                    urlencode($this->assetUrls[$key]['suffix'])
                );
            } else {
                $url = sprintf(
                    '%s&%s',
                    $url,
                    urlencode($this->assetUrls[$key]['suffix'])
                );
            }
        }

        return $url;
    }

    /**
     * Sets a bunch of asset urls.
     *
     * @param  array $assetUrls
     * @throws Exception\InvalidArgumentException
     */
    public function setAssetUrls($assetUrls = array())
    {
        if (!is_array($assetUrls)) {
            throw new Exception\InvalidArgumentException(sprintf(
                'Parameter "%s" needs to be specified as %s.',
                'assetUrls',
                'an array'
            ));
        }

        $this->defaultKey = null;
        $this->assetUrls = array();

        foreach ($assetUrls as $key => $assetUrlOptions) {
            $this->setAssetUrl(
                $key,
                isset($assetUrlOptions['prefix']) ? $assetUrlOptions['prefix'] : '',
                isset($assetUrlOptions['suffix']) ? $assetUrlOptions['suffix'] : ''
            );
        }

    }

    /**
     * Sets an asset url.
     *
     * @param  string $key
     * @param  string $prefix
     * @param  string $suffix
     * @throws Exception\InvalidArgumentException
     */
    public function setAssetUrl($key = null, $prefix = '', $suffix = '')
    {
        if (!is_string($key)) {
            throw new Exception\InvalidArgumentException(sprintf(
                'Parameter "%s" needs to be specified as %s.',
                'key',
                'a string'
            ));
        }

        if (!is_string($prefix)) {
            throw new Exception\InvalidArgumentException(sprintf(
                'Parameter "%s" needs to be specified as %s.',
                'prefix',
                'a string'
            ));
        }

        if (!is_string($suffix)) {
            throw new Exception\InvalidArgumentException(sprintf(
                'Parameter "%s" needs to be specified as %s.',
                'suffix',
                'a string'
            ));
        }

        $prefix = rtrim(
            $prefix,
            '/'
        );

        if ('' == $prefix) {
            $prefix = null;
        }

        $suffix = ltrim(
            $suffix,
            '?'
        );

        if ('' == $suffix) {
            $suffix = null;
        }

        $this->assetUrls[$key] = array(
            'prefix' => $prefix,
            'suffix' => $suffix,
        );
    }

    /**
     * Returns default key.
     *
     * @return null|string
     */
    public function getDefaultKey()
    {
        if (null === $this->defaultKey
            && is_array($this->assetUrls)
            && 0 < count($this->assetUrls)) {

            $keys = array_keys($this->assetUrls);
            $this->defaultKey = reset($keys);

        }

        return $this->defaultKey;
    }

    /**
     * Sets default key.
     *
     * @param  string $key
     * @throws Exception\InvalidArgumentException
     */
    public function setDefaultKey($key)
    {
        if (!is_string($key)) {
            throw new Exception\InvalidArgumentException(sprintf(
                'Parameter "%s" needs to be specified as %s.',
                'file',
                'a string'
            ));
        }

        $this->defaultKey = $key;
    }

}
