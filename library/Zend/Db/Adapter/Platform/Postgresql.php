<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Db\Adapter\Platform;

use Zend\Db\Adapter\Driver\Pgsql;
use Zend\Db\Adapter\Driver\Pdo;
use Zend\Db\Adapter\Exception;

class Postgresql implements PlatformInterface
{
    /** @var resource|\PDO */
    protected $resource = null;

    public function __construct($driver = null)
    {
        if ($driver) {
            $this->setDriver($driver);
        }
    }

    /**
     * @param \Zend\Db\Adapter\Driver\Pgsql\Pgsql|\Zend\Db\Adapter\Driver\Pdo\Pdo|resource|\PDO $driver
     * @throws \Zend\Db\Adapter\Exception\InvalidArgumentException
     * @return $this
     */
    public function setDriver($driver)
    {
        if ($driver instanceof Pgsql
            || ($driver instanceof Pdo\Pdo && $driver->getDatabasePlatformName() == 'Postgresql')
        ) {
            $this->resource = $driver->getConnection()->getResource();
            return $this;
        }

        if ((is_resource($driver) && (in_array(get_resource_type($driver), array('pgsql link', 'pgsql link persistent'))))
            || ($driver instanceof \PDO && $driver->getAttribute(\PDO::ATTR_DRIVER_NAME) == 'pgsql')
        ) {
            $this->resource = $driver;
            return $this;
        }

        throw new Exception\InvalidArgumentException('$driver must be a Pgsql or Postgresql PDO Zend\Db\Adapter\Driver, pgsql link resource or Postgresql PDO instance');
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return 'PostgreSQL';
    }

    /**
     * Get quote indentifier symbol
     *
     * @return string
     */
    public function getQuoteIdentifierSymbol()
    {
        return '"';
    }

    /**
     * Quote identifier
     *
     * @param  string $identifier
     * @return string
     */
    public function quoteIdentifier($identifier)
    {
        return '"' . str_replace('"', '\\' . '"', $identifier) . '"';
    }

    /**
     * Quote identifier chain
     *
     * @param string|string[] $identifierChain
     * @return string
     */
    public function quoteIdentifierChain($identifierChain)
    {
        $identifierChain = str_replace('"', '\\"', $identifierChain);
        if (is_array($identifierChain)) {
            $identifierChain = implode('"."', $identifierChain);
        }
        return '"' . $identifierChain . '"';
    }

    /**
     * Get quote value symbol
     *
     * @return string
     */
    public function getQuoteValueSymbol()
    {
        return '\'';
    }

    /**
     * Quote value
     *
     * @param  string $value
     * @return string
     */
    public function quoteValue($value)
    {
        if (is_resource($this->resource)) {
            return '\'' . pg_escape_string($this->resource, $value) . '\'';
        }
        if ($this->resource instanceof \PDO) {
            return $this->resource->quote($value);
        }
        trigger_error(
            'Attempting to quote a value in ' . __CLASS__
                . ' without providing a driver/resource is not a practice you should rely on in production systems'
        );
        return '\'' . addcslashes($value, '\\\'') . '\'';
    }

    /**
     * Quote value list
     *
     * @param string|string[] $valueList
     * @return string
     */
    public function quoteValueList($valueList)
    {
        if (is_array($valueList)) {
            $value = reset($valueList);
            do {
                $valueList[key($valueList)] = $this->quoteValue($value);
            } while ($value = next($valueList));
            return '\'' . implode('\', \'', $valueList) . '\'';
        } else {
            return '\'' .  $this->quoteValue($valueList) . '\'';
        }
    }

    /**
     * Get identifier separator
     *
     * @return string
     */
    public function getIdentifierSeparator()
    {
        return '.';
    }

    /**
     * Quote identifier in fragment
     *
     * @param  string $identifier
     * @param  array $safeWords
     * @return string
     */
    public function quoteIdentifierInFragment($identifier, array $safeWords = array())
    {
        $parts = preg_split('#([\.\s\W])#', $identifier, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
        if ($safeWords) {
            $safeWords = array_flip($safeWords);
            $safeWords = array_change_key_case($safeWords, CASE_LOWER);
        }
        foreach ($parts as $i => $part) {
            if ($safeWords && isset($safeWords[strtolower($part)])) {
                continue;
            }
            switch ($part) {
                case ' ':
                case '.':
                case '*':
                case 'AS':
                case 'As':
                case 'aS':
                case 'as':
                    break;
                default:
                    $parts[$i] = '"' . str_replace('"', '\\' . '"', $part) . '"';
            }
        }
        return implode('', $parts);
    }
}
