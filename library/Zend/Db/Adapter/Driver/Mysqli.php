<?php

namespace Zend\Db\Adapter\Driver;

class Mysqli implements \Zend\Db\Adapter\Driver
{

    /**
     * @var Mysqli\Connection
     */
    protected $connection = null;

    /**
     * @var Mysqli\Statement
     */
    protected $statementPrototype = null;

    /**
     * @var Mysqli\Result
     */
    protected $resultPrototype = null;

    /**
     * @param array|Mysqli\Connection $connection
     * @param null|Mysqli\Statement $statementPrototype
     * @param null|Mysqli\Result $resultPrototype
     */
    public function __construct($connection, Mysqli\Statement $statementPrototype = null, Mysqli\Result $resultPrototype = null)
    {
        if (is_array($connection)) {
            $connection = new Mysqli\Connection($connection);
        }

        if (!$connection instanceof Mysqli\Connection) {
            throw new \InvalidArgumentException('$connection must be an array of parameters or a Pdo\Connection object');
        }

        $this->registerConnection($connection);
        $this->registerStatementPrototype(($statementPrototype) ?: new Mysqli\Statement());
        $this->registerResultPrototype(($resultPrototype) ?: new Mysqli\Result());
    }

    public function registerConnection(Mysqli\Connection $connection)
    {
        $this->connection = $connection;
        $this->connection->setDriver($this);
        return $this;
    }

    public function registerStatementPrototype(Mysqli\Statement $statementPrototype)
    {
        $this->statementPrototype = $statementPrototype;
        $this->statementPrototype->setDriver($this);
    }

    public function registerResultPrototype(Mysqli\Result $resultPrototype)
    {
        $this->resultPrototype = $resultPrototype;
        $this->resultPrototype->setDriver($this);
    }

    public function getDatabasePlatformName($nameFormat = self::NAME_FORMAT_CAMELCASE)
    {
        if ($nameFormat == self::NAME_FORMAT_CAMELCASE) {
            return 'Mysql';
        } else {
            return 'MySQL';
        }
    }
    
    public function checkEnvironment()
    {
        if (!extension_loaded('mysqli')) {
            throw new \Exception('The Mysqli extension is required for this adapter but the extension is not loaded');
        }
    }

    /**
     * @return Mysqli\Connection
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * @return Mysqli\Statement
     */
    public function getStatementPrototype()
    {
        return $this->statementPrototype;
    }

    /**
     * @return Mysqli\Result
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
