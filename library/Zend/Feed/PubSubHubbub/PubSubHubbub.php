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
 * @package    Zend_Feed_Pubsubhubbub
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Feed\PubSubHubbub;

use Zend\Feed\Reader;
use Zend\Http;

/**
 * @category   Zend
 * @package    Zend_Feed_Pubsubhubbub
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class PubSubHubbub
{
    /**
     * Verification Modes
     */
    const VERIFICATION_MODE_SYNC  = 'sync';
    const VERIFICATION_MODE_ASYNC = 'async';

    /**
     * Subscription States
     */
    const SUBSCRIPTION_VERIFIED    = 'verified';
    const SUBSCRIPTION_NOTVERIFIED = 'not_verified';
    const SUBSCRIPTION_TODELETE    = 'to_delete';

    /**
     * Singleton instance if required of the HTTP client
     *
     * @var \Zend\Http\Client
     */
    protected static $httpClient = null;

    /**
     * Simple utility function which imports any feed URL and
     * determines the existence of Hub Server endpoints. This works
     * best if directly given an instance of Zend_Feed_Reader_Atom|Rss
     * to leverage off.
     *
     * @param  \Zend\Feed\Reader\AbstractFeed|string $source
     * @return array
     */
    public static function detectHubs($source)
    {
        if (is_string($source)) {
            $feed = Reader\Reader::import($source);
        } elseif (is_object($source) && $source instanceof Reader\Feed\AbstractFeed) {
            $feed = $source;
        } else {
            throw new Exception('The source parameter was'
            . ' invalid, i.e. not a URL string or an instance of type'
            . ' Zend\Feed\Reader\FeedAbstract or Zend\Feed\Abstract');
        }
        return $feed->getHubs();
    }

    /**
     * Allows the external environment to make Zend_Oauth use a specific
     * Client instance.
     *
     * @param  \Zend\Http\Client $httpClient
     * @return void
     */
    public static function setHttpClient(Http\Client $httpClient)
    {
        self::$httpClient = $httpClient;
    }

    /**
     * Return the singleton instance of the HTTP Client. Note that
     * the instance is reset and cleared of previous parameters GET/POST.
     * Headers are NOT reset but handled by this component if applicable.
     *
     * @return \Zend\Http\Client
     */
    public static function getHttpClient()
    {
        if (!isset(self::$httpClient)):
            self::$httpClient = new Http\Client;
        else:
            self::$httpClient->resetParameters();
        endif;
        return self::$httpClient;
    }

    /**
     * Simple mechanism to delete the entire singleton HTTP Client instance
     * which forces an new instantiation for subsequent requests.
     *
     * @return void
     */
    public static function clearHttpClient()
    {
        self::$httpClient = null;
    }

    /**
     * RFC 3986 safe url encoding method
     *
     * @param  string $string
     * @return string
     */
    public static function urlencode($string)
    {
        $rawencoded = rawurlencode($string);
        $rfcencoded = str_replace('%7E', '~', $rawencoded);
        return $rfcencoded;
    }
}
