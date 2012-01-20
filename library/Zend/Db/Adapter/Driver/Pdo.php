<?php

namespace Zend\Db\Adapter\Driver;


class Pdo implements \Zend\Db\Adapter\Driver
{
    /**
     * @var Pdo\Connection
     */
    protected $connection = null;

    /**
     * @var Pdo\Statement
     */
    protected $statementPrototype = null;

    /**
     * @var Pdo\Result
     */
    protected $resultPrototype = null;

    /**
     * @param d$connection
     * @param null|Pdo\Statement $statementPrototype
     * @param Pdo\Result $resultPrototype
     */
    public function __construct($connection, Pdo\Statement $statementPrototype = null, Pdo\Result $resultPrototype = null)
    {
        if (is_array($connection)) {
            $connection = new Pdo\Connection($connection);
        }

        if (!$connection instanceof Pdo\Connection) {
            throw new \InvalidArgumentException('$connection must be an array of parameters or a Pdo\Connection object');
        }

        $this->registerConnection($connection);
        $this->registerStatementPrototype(($statementPrototype) ?: new Pdo\Statement());
        $this->registerResultPrototype(($resultPrototype) ?: new Pdo\Result());
    }

    public function registerConnection(Pdo\Connection $connection)
    {
        $this->connection = $connection;
        $this->connection->setDriver($this);
        return $this;
    }

    public function registerStatementPrototype(Pdo\Statement $statementPrototype)
    {
        $this->statementPrototype = $statementPrototype;
        $this->statementPrototype->setDriver($this);
    }

    public function registerResultPrototype(Pdo\Result $resultPrototype)
    {
        $this->resultPrototype = $resultPrototype;
        $this->resultPrototype->setDriver($this);
    }

    public function getDatabasePlatformName($nameFormat = self::NAME_FORMAT_CAMELCASE)
    {
        // have to pull this from the dsn
        var_dump($this->getConnection());
    }
    
    public function checkEnvironment()
    {
        if (!extension_loaded('PDO')) {
            throw new \Exception('The PDO extension is required for this adapter but the extension is not loaded');
        }
    }

    /**
     * @return Zend\Db\Adapter\DriverConnection
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * @return Zend\Db\Adapter\DriverStatement
     */
    public function getStatementPrototype()
    {
        return $this->statementPrototype;
    }

    /**
     * @return Zend\Db\Adapter\DriverResult
     */
    public function getResultPrototype()
    {
        return $this->resultPrototype;
    }

    /**
     * @return array
     */
    public function getPrepareTypeSupport()
    {
        // TODO: Implement getPrepareTypeSupport() method.
    }

    /**
     * @param $name
     * @return string
     */
    public function formatNamedParameter($name)
    {
        // TODO: Implement formatNamedParameter() method.
    }

}
