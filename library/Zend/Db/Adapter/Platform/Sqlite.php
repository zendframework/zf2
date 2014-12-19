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

class Sqlite extends AbstractSql92BasedPlatform
{
    /** @var \PDO */
    protected $resource = null;

    public function __construct($driver = null)
    {
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
        if (($driver instanceof \PDO && $driver->getAttribute(\PDO::ATTR_DRIVER_NAME) == 'sqlite')
            || ($driver instanceof Pdo\Pdo && $driver->getDatabasePlatformName() == 'Sqlite')
        ) {
            $this->resource = $driver;
            return $this;
        }

        throw new Exception\InvalidArgumentException('$driver must be a Sqlite PDO Zend\Db\Adapter\Driver, Sqlite PDO instance');
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return 'SQLite';
    }

    /**
     * Quote value
     *
     * @param  string $value
     * @return string
     */
    public function quoteValue($value)
    {
        $resource = $this->resource;

        if ($resource instanceof DriverInterface) {
            $resource = $resource->getConnection()->getResource();
        }

        if ($resource instanceof \PDO) {
            return $resource->quote($value);
        }

        return parent::quoteValue($value);
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
        $resource = $this->resource;

        if ($resource instanceof DriverInterface) {
            $resource = $resource->getConnection()->getResource();
        }

        if ($resource instanceof \PDO) {
            return $resource->quote($value);
        }

        return parent::quoteTrustedValue($value);
    }
}
