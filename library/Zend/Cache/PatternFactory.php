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
 * @package    Zend_Cache
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Cache;

use Zend\Loader\Broker;

/**
 * @category   Zend
 * @package    Zend_Cache
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class PatternFactory
{
    /**
     * The pattern broker
     *
     * @var null|Broker
     */
    protected static $broker = null;

    /**
     * Instantiate a cache pattern
     *
     * @param  string|Pattern $patternName
     * @param  array|Traversable $options
     * @return Pattern
     * @throws Exception\RuntimeException
     */
    public static function factory($patternName, $options = array())
    {
        if ($patternName instanceof Pattern) {
            $patternName->setOptions($options);
            return $patternName;
        }

        return static::getBroker()->load($patternName, $options);
    }

    /**
     * Get the pattern broker
     *
     * @return Broker
     */
    public static function getBroker()
    {
        if (static::$broker === null) {
            static::$broker = static::getDefaultBroker();
        }

        return static::$broker;
    }

    /**
     * Set the pattern broker
     *
     * @param  Broker $broker
     * @return void
     */
    public static function setBroker(Broker $broker)
    {
        static::$broker = $broker;
    }

    /**
     * Reset pattern broker to default
     *
     * @return void
     */
    public static function resetBroker()
    {
        static::$broker = null;
    }

    /**
     * Get internal pattern broker
     *
     * @return PatternBroker
     */
    protected static function getDefaultBroker()
    {
        return new PatternBroker();
    }
}
