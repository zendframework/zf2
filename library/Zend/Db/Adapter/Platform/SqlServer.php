<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Db\Adapter\Platform;

use Zend\Db\Adapter\Driver\DriverInterface;
use Zend\Db\Adapter\Driver\Pdo;
use Zend\Db\Adapter\Exception;

class SqlServer extends AbstractPlatform
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
     * @param \Zend\Db\Adapter\Driver\Sqlsrv\Sqlsrv|\Zend\Db\Adapter\Driver\Pdo\Pdo||resource|\PDO $driver
     * @throws \Zend\Db\Adapter\Exception\InvalidArgumentException
     * @return $this
     */
    public function setDriver($driver)
    {
        // handle Zend_Db drivers
        if (($driver instanceof Pdo\Pdo && in_array($driver->getDatabasePlatformName(), array('Sqlsrv', 'Dblib')))
            || (($driver instanceof \PDO && in_array($driver->getAttribute(\PDO::ATTR_DRIVER_NAME), array('sqlsrv', 'dblib'))))
        ) {
            $this->resource = $driver;
            return $this;
        }

        throw new Exception\InvalidArgumentException('$driver must be a Sqlsrv PDO Zend\Db\Adapter\Driver or Sqlsrv PDO instance');
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return 'SQLServer';
    }

    /**
     * Get quote identifier symbol
     *
     * @return string
     */
    public function getQuoteIdentifierSymbol()
    {
        return array('[', ']');
    }

    /**
     * Quote identifier
     *
     * @param  string $identifier
     * @return string
     */
    public function quoteIdentifier($identifier)
    {
        return '[' . $identifier . ']';
    }

    /**
     * Quote identifier chain
     *
     * @param string|string[] $identifierChain
     * @return string
     */
    public function quoteIdentifierChain($identifierChain)
    {
        if (is_array($identifierChain)) {
            $identifierChain = implode('].[', $identifierChain);
        }
        return '[' . $identifierChain . ']';
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
        if ($this->resource instanceof \PDO) {
            return $this->resource->quote($value);
        }
        trigger_error(
            'Attempting to quote a value in ' . __CLASS__ . ' without extension/driver support '
                . 'can introduce security vulnerabilities in a production environment.'
        );
        return '\'' . str_replace('\'', '\'\'', $value) . '\'';
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
        if ($this->resource instanceof DriverInterface) {
            $this->resource = $this->resource->getConnection()->getResource();
        }
        if ($this->resource instanceof \PDO) {
            return $this->resource->quote($value);
        }
        return '\'' . str_replace('\'', '\'\'', $value) . '\'';
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

}
