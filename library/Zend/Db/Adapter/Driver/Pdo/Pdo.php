<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Db
 */

namespace Zend\Db\Adapter\Driver\Pdo;

use PDOStatement;
use Zend\Db\Adapter\Driver\DriverInterface;
use Zend\Db\Adapter\Driver\Feature\AbstractFeature;
use Zend\Db\Adapter\Driver\Feature\DriverFeatureInterface;
use Zend\Db\Adapter\Exception;

/**
 * @category   Zend
 * @package    Zend_Db
 * @subpackage Adapter
 */
class Pdo implements DriverInterface, DriverFeatureInterface
{
    /**
     * @const
     */
    const FEATURES_DEFAULT = 'default';

    /**
     * @var Connection
     */
    protected $connection = null;

    /**
     * @var Statement
     */
    protected $statementPrototype = null;

    /**
     * @var Result
     */
    protected $resultPrototype = null;

    /**
     * @var array
     */
    protected $features = array();

    /**
     * @param array|Connection|\PDO $connection
     * @param null|Statement $statementPrototype
     * @param null|Result $resultPrototype
     * @param string $features
     */
    public function __construct($connection, Statement $statementPrototype = null, Result $resultPrototype = null, $features = self::FEATURES_DEFAULT)
    {
        if (!$connection instanceof Connection) {
            $connection = new Connection($connection);
        }

        $this->registerConnection($connection);
        $this->registerStatementPrototype(($statementPrototype) ?: new Statement());
        $this->registerResultPrototype(($resultPrototype) ?: new Result());
        if (is_array($features)) {
            foreach ($features as $name => $feature) {
                $this->addFeature($name, $feature);
            }
        } elseif ($features instanceof AbstractFeature) {
            $this->addFeature($features->getName(), $features);
        } elseif ($features === self::FEATURES_DEFAULT) {
            $this->setupDefaultFeatures();
        }
    }

    /**
     * Register connection
     *
     * @param  Connection $connection
     * @return Pdo
     */
    public function registerConnection(Connection $connection)
    {
        $this->connection = $connection;
        $this->connection->setDriver($this);
        return $this;
    }

    /**
     * Register statement prototype
     *
     * @param Statement $statementPrototype
     */
    public function registerStatementPrototype(Statement $statementPrototype)
    {
        $this->statementPrototype = $statementPrototype;
        $this->statementPrototype->setDriver($this);
    }

    /**
     * Register result prototype
     *
     * @param Result $resultPrototype
     */
    public function registerResultPrototype(Result $resultPrototype)
    {
        $this->resultPrototype = $resultPrototype;
    }

    /**
     * @param string $name
     * @param AbstractFeature $feature
     * @return Pdo
     */
    public function addFeature($name, $feature)
    {
        if ($feature instanceof AbstractFeature) {
            $name = $feature->getName(); // overwrite the name, just in case
            $feature->setDriver($this);
        }
        $this->features[$name] = $feature;
        return $this;
    }

    /**
     * setup the default features for Pdo
     */
    public function setupDefaultFeatures()
    {
        if ($this->connection->getDriverName() == 'sqlite') {
            $this->addFeature(null, new Feature\SqliteRowCounter);
        }
        return $this;
    }

    /**
     * @param $name
     * @return AbstractFeature|false
     */
    public function getFeature($name)
    {
        if (isset($this->features[$name])) {
            return $this->features[$name];
        }
        return false;
    }

    /**
     * Get database platform name
     *
     * @param  string $nameFormat
     * @return string
     */
    public function getDatabasePlatformName($nameFormat = self::NAME_FORMAT_CAMELCASE)
    {
        $name = $this->getConnection()->getDriverName();
        if ($nameFormat == self::NAME_FORMAT_CAMELCASE) {
            switch ($name) {
                case 'pgsql':
                    return 'Postgresql';
                default:
                    return ucfirst($name);
            }
        } else {
            switch ($name) {
                case 'sqlite':
                    return 'SQLite';
                case 'mysql':
                    return 'MySQL';
                case 'pgsql':
                    return 'PostgreSQL';
                default:
                    return ucfirst($name);
            }
        }
    }

    /**
     * Check environment
     */
    public function checkEnvironment()
    {
        if (!extension_loaded('PDO')) {
            throw new Exception\RuntimeException('The PDO extension is required for this adapter but the extension is not loaded');
        }
    }

    /**
     * @return Connection
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * @param string|PDOStatement $sqlOrResource
     * @return Statement
     */
    public function createStatement($sqlOrResource = null)
    {
        $statement = clone $this->statementPrototype;
        if ($sqlOrResource instanceof PDOStatement) {
            $statement->setResource($sqlOrResource);
        } else {
            if (is_string($sqlOrResource)) {
                $statement->setSql($sqlOrResource);
            }
            if (!$this->connection->isConnected()) {
                $this->connection->connect();
            }
            $statement->initialize($this->connection->getResource());
        }
        return $statement;
    }

    /**
     * @param resource $resource
     * @param mixed $context
     * @return Result
     */
    public function createResult($resource, $context = null)
    {
        $result = clone $this->resultPrototype;
        $rowCount = null;

        // special feature, sqlite PDO counter
        if ($this->connection->getDriverName() == 'sqlite'
            && ($sqliteRowCounter = $this->getFeature('SqliteRowCounter'))
            && $resource->columnCount() > 0) {
            $rowCount = $sqliteRowCounter->getRowCountClosure($context);
        }

        $result->initialize($resource, $this->connection->getLastGeneratedValue(), $rowCount);
        return $result;
    }

    /**
     * @return array
     */
    public function getPrepareType()
    {
        return self::PARAMETERIZATION_NAMED;
    }

    /**
     * @param string $name
     * @param string|null $type
     * @return string
     */
    public function formatParameterName($name, $type = null)
    {
        if ($type == null && !is_numeric($name) || $type == self::PARAMETERIZATION_NAMED) {
            return ':' . $name;
        } else {
            return '?';
        }
    }

    /**
     * @return mixed
     */
    public function getLastGeneratedValue()
    {
        return $this->connection->getLastGeneratedValue();
    }

}
