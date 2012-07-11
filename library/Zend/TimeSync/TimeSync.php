<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_TimeSync
 */

namespace Zend\TimeSync;

use ArrayObject;
use DateTime;
use IteratorAggregate;
use Zend\TimeSync\Exception;

/**
 * @category   Zend
 * @package    Zend_TimeSync
 */
class TimeSync implements IteratorAggregate
{
    /**
     * Set the default timeserver protocol to "Ntp". This will be called
     * when no protocol is specified
     */
    const DEFAULT_PROTOCOL = 'Ntp';

    /**
     * Contains array of timeserver objects
     *
     * @var AbstractProtocol[]
     */
    protected $timeservers = array();

    /**
     * Holds a reference to the timeserver that is currently being used
     *
     * @var AbstractProtocol
     */
    protected $current;

    /**
     * Allowed timeserver schemes
     *
     * @var array
     */
    protected $allowedSchemes = array(
        'Ntp',
        'Sntp',
    );

    /**
     * Configuration array, set using the constructor or using
     * ::setOptions() or ::setOption()
     *
     * @var array
     */
    public static $options = array(
        'timeout' => 1
    );

    /**
     * Constructor
     *
     * @param string|array $target OPTIONAL single timeserver, or an array of timeservers.
     * @param string       $alias  OPTIONAL an alias for this timeserver
     */
    public function __construct($target = null, $alias = null)
    {
        if ($target !== null) {
            $this->addServer($target, $alias);
        }
    }

    /**
     * getIterator() - return an iteratable object for use in foreach and the like,
     * this completes the IteratorAggregate interface
     *
     * @return ArrayObject
     */
    public function getIterator()
    {
        return new ArrayObject($this->timeservers);
    }

    /**
     * Add a timeserver or multiple timeservers
     *
     * Server should be a single string representation of a timeserver,
     * or a structured array listing multiple timeservers.
     *
     * If you provide an array of timeservers in the $target variable,
     * $alias will be ignored. you can enter these as the array key
     * in the provided array, which should be structured as follows:
     *
     * <code>
     * $example = array(
     *   'server_a' => 'ntp://127.0.0.1',
     *   'server_b' => 'ntp://127.0.0.1:123',
     *   'server_c' => 'ntp://[2000:364:234::2.5]',
     *   'server_d' => 'ntp://[2000:364:234::2.5]:123'
     * );
     * </code>
     *
     * If no port number has been supplied, the default matching port
     * number will be used.
     *
     * Supported protocols are:
     * - ntp
     * - sntp
     *
     * @param  string|array $target Single timeserver, or an array of timeservers.
     * @param  string       $alias  OPTIONAL an alias for this timeserver
     * @throws Exception\ExceptionInterface
     */
    public function addServer($target, $alias = null)
    {
        if (is_array($target)) {
            foreach ($target as $alias => $server) {
                $this->addAServer($server, $alias);
            }
        } else {
            $this->addAServer($target, $alias);
        }
    }

    /**
     * Sets the value for the given options
     *
     * This will replace any currently defined options.
     *
     * @param array $options An array of options to be set
     */
    public static function setOptions(array $options)
    {
        foreach ($options as $key => $value) {
            self::$options[$key] = $value;
        }
    }

    /**
     * Marks a timeserver as current
     *
     * @param  string|integer $alias The alias from the timeserver to set as current
     * @throws Exception\InvalidArgumentException
     */
    public function setServer($alias)
    {
        if (isset($this->timeservers[$alias])) {
            $this->current = $this->timeservers[$alias];
        } else {
            throw new Exception\InvalidArgumentException("'$alias' does not point to valid timeserver");
        }
    }

    /**
     * Returns the value to the option
     *
     * @param  string $key The option's identifier
     * @return mixed
     * @throws Exception\OutOfBoundsException
     */
    public static function getOptions($key = null)
    {
        if ($key == null) {
            return self::$options;
        }

        if (isset(self::$options[$key])) {
            return self::$options[$key];
        } else {
            throw new Exception\OutOfBoundsException("'$key' does not point to valid option");
        }
    }

    /**
     * Return a specified timeserver by alias
     * If no alias is given it will return the current timeserver
     *
     * @param  string|integer $alias The alias from the timeserver to return
     * @return AbstractProtocol
     * @throws Exception\InvalidArgumentException
     */
    public function getServer($alias = null)
    {
        if ($alias === null) {
            if (isset($this->current) && $this->current !== false) {
                return $this->current;
            } else {
                throw new Exception\InvalidArgumentException('there is no timeserver set');
            }
        }
        if (isset($this->timeservers[$alias])) {
            return $this->timeservers[$alias];
        } else {
            throw new Exception\InvalidArgumentException("'$alias' does not point to valid timeserver");
        }
    }

    /**
     * Returns information sent/returned from the current timeserver
     *
     * @return array
     */
    public function getInfo()
    {
        return $this->getServer()->getInfo();
    }

    /**
     * Query the timeserver list using the fallback mechanism
     *
     * If there are multiple servers listed, this method will act as a
     * facade and will try to return the date from the first server that
     * returns a valid result.
     *
     * @return DateTime
     * @throws Exception\RuntimeException
     */
    public function getDate()
    {
        foreach ($this->timeservers as $server) {
            $this->current = $server;
            try {
                return $server->getDate();
            } catch (Exception\RuntimeException $e) {
                if (!isset($masterException)) {
                    $masterException = new Exception\RuntimeException($e->getMessage(), $e->getCode());
                } else {
                    $masterException = new Exception\RuntimeException($e->getMessage(), $e->getCode(), $masterException);
                }
            }
        }

        throw new Exception\RuntimeException('All timeservers are bogus', 0, $masterException);
    }

    /**
     * Adds a timeserver object to the timeserver list
     *
     * @param  string $target Single timeserver.
     * @param  string $alias  An alias for this timeserver
     * @throws Exception\RuntimeException
     */
    protected function addAServer($target, $alias)
    {
        $pos = strpos($target, '://');
        if ($pos) {
            $protocol = substr($target, 0, $pos);
            $address  = substr($target, $pos + 3);
        } else {
            $address  = $target;
            $protocol = self::DEFAULT_PROTOCOL;
        }

        $pos = strrpos($address, ':');
        if ($pos) {
            $posbr = strpos($address, ']');
            if ($posbr and ($pos > $posbr)) {
                $port = substr($address, $pos + 1);
                $address = substr($address, 0, $pos);
            } elseif (!$posbr and $pos) {
                $port = substr($address, $pos + 1);
                $address = substr($address, 0, $pos);
            } else {
                $port = null;
            }
        } else {
            $port = null;
        }

        $protocol = ucfirst(strtolower($protocol));
        if (!in_array($protocol, $this->allowedSchemes)) {
            throw new Exception\RuntimeException("'$protocol' is not a supported protocol");
        }

        $className = 'Zend\\TimeSync\\' . $protocol;
        if ($port) {
            $timeServerObj = new $className($address, $port);
        } else {
            $timeServerObj = new $className($address);
        }

        $this->timeservers[$alias] = $timeServerObj;
    }
}
