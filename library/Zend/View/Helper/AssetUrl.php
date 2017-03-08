<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\View\Helper;

use Zend\View\Exception;

/**
 * Helper for retrieving an asset URL.
 *
 * @package    Zend_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class AssetUrl extends AbstractHelper
{
    /**
     * Base URL for assets.
     *
     * @var string
     */
    protected $assetBaseUrl;
    
    /**
     * Suffix for assets.
     *
     * @var string
     */
    protected $suffix;

    /**
     * Returns the URL for an asset.
     *
     * If a suffix was set, it will be appended as query parameter to the URL.
     *
     * @param  string  $file
     * @parma  boolean $omitSuffix
     * @return string
     */
    public function __invoke($file, $omitSuffix = false)
    {
        if (null === $this->assetBaseUrl) {
            throw new Exception\RuntimeException('No asset base URL provided');
        }

        $url = $this->assetBaseUrl . '/' . ltrim($file, '/');
        
        if (!$omitSuffix && null !== $this->suffix) {
            if (strpos($url, '?') === false) {
                $url .= '?' . $this->suffix;
            } else {
                $url .= '&' . $this->suffix;
            }
        }

        return $url;
    }

    /**
     * Set the asset base URL.
     *
     * @param  string $assetBaseUrl
     * @return AssetUrl
     */
    public function setAssetBaseUrl($assetBaseUrl)
    {
        $this->assetBaseUrl = rtrim($assetBaseUrl, '/');
        return $this;
    }

    /**
     * Set a suffix for assets.
     *
     * @param  string $suffix
     * @return AssetUrl
     */
    public function setSuffix($suffix)
    {
        $this->suffix = urlencode($suffix);
        return $this;
    }
}
