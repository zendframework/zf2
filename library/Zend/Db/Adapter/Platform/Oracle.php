<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Db\Adapter\Platform;

use Zend\Db\Adapter\Driver\DriverInterface;
use Zend\Db\Adapter\Driver\Oci8;
use Zend\Db\Adapter\Driver\Pdo;
use Zend\Db\Adapter\Exception;

class Oracle implements PlatformInterface
{

    /**
     * @var bool
     */
    protected $quoteIdentifiers = true;

    /** @var resource|\PDO */
    protected $resource = null;

    /**
     * @param array $options
     */
    public function __construct($driver = null, $options = array())
    {
        if (isset($options['quote_identifiers'])
            && ($options['quote_identifiers'] == false
            || $options['quote_identifiers'] === 'false')
        ) {
            $this->quoteIdentifiers = false;
        }
        
        if ($driver) {
            $this->setDriver($driver);
        }
    }

    /**
     * @param \Zend\Db\Adapter\Driver\Pdo\Pdo||\PDO $driver
     * @throws \Zend\Db\Adapter\Exception\InvalidArgumentException
     * @return $this
     */
    public function setDriver($driver)
    {
        if ($driver instanceof Oci8\Oci8
            || ($driver instanceof Pdo\Pdo && $driver->getDatabasePlatformName() == 'Oracle')
            || ($driver instanceof Pdo\Pdo && $driver->getDatabasePlatformName() == 'Sqlite')
            || ($driver instanceof \oci8)
            || ($driver instanceof \PDO && $driver->getAttribute(\PDO::ATTR_DRIVER_NAME) == 'oci')
        ) {
            $this->resource = $driver;
            return $this;
        }

        throw new Exception\InvalidArgumentException('$driver must be a Oci8 or Mysql PDO Zend\Db\Adapter\Driver, Oci8 instance or Oci PDO instance');
    }
    
    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return 'Oracle';
    }

    /**
     * Get quote identifier symbol
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
        if ($this->quoteIdentifiers === false) {
            return $identifier;
        }
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
        if ($this->quoteIdentifiers === false) {
            return (is_array($identifierChain)) ? implode('.', $identifierChain) : $identifierChain;
        }
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
        if ($this->resource instanceof DriverInterface) {
            $this->resource = $this->resource->getConnection()->getResource();   
        }        
        
        if($this->resource){
            // @todo check extension/driver security
            if ($this->resource instanceof \PDO || 
                    get_resource_type($this->resource) == 'oci8 connection' || 
                    get_resource_type($this->resource) == 'oci8 persistent connection') {
                return '\'' . addcslashes($value, "\x00\n\r\\'\"\x1a") . '\'';
            }
        }
        
        trigger_error(
            'Attempting to quote a value in ' . __CLASS__ . ' without extension/driver support '
                . 'can introduce security vulnerabilities in a production environment.'
        );
        return '\'' . addcslashes($value, "\x00\n\r\\'\"\x1a") . '\'';
    }

    /**
     * Quote Trusted Value
     *
     * The ability to quote values without notices
     *
     * @param $value
     * @return mixed
     */
    public function quoteTrustedValue($value)
    {
        return '\'' . addcslashes($value, "\x00\n\r\\'\"\x1a") . '\'';
    }

    /**
     * Quote value list
     *
     * @param string|string[] $valueList
     * @return string
     */
    public function quoteValueList($valueList)
    {
        if (!is_array($valueList)) {
            return $this->quoteValue($valueList);
        }

        $value = reset($valueList);
        do {
            $valueList[key($valueList)] = $this->quoteValue($value);
        } while ($value = next($valueList));
        return implode(', ', $valueList);
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
        if ($this->quoteIdentifiers === false) {
            return $identifier;
        }
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
