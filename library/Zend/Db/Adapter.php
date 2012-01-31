<?php
/**
 * File DocBlock
 */

namespace Zend\Db;

/**
 * Class DocBlock
 *
 * @property Adapter\Driver $driver
 * @property Adapter\Platform $platform
 */
class Adapter
{
    /**
     * Query Mode Constants
     */
    const QUERY_MODE_EXECUTE = 'execute';
    const QUERY_MODE_PREPARE = 'prepare';

    /**
     * Prepare Type Constants
     */
    const PREPARE_TYPE_POSITIONAL = 'positional';
    const PREPARE_TYPE_NAMED = 'named';

    /**
     * Built-in namespaces
     */
    const BUILTIN_DRIVERS_NAMESPACE = 'Zend\Db\Adapter\Driver';
    const BUILTIN_PLATFORMS_NAMESPACE = 'Zend\Db\Adapter\Platform';

    /**
     * @var Adapter\Driver
     */
    protected $driver = null;

    /**
     * @var Adapter\Platform
     */
    protected $platform = null;

    /**
     * @var \Zend\Db\ResultSet\ResultSet
     */
    protected $queryResultSetPrototype = null;

    /**
     * @var string
     */
    protected $queryMode = self::QUERY_MODE_PREPARE;


    /**
     * @param Adapter\Driver|array $driver
     * @param Adapter\Platform $platform
     * @param ResultSet\ResultSet $queryResultPrototype
     */
    public function __construct($driver, Adapter\Platform $platform = null, ResultSet\ResultSet $queryResultPrototype = null)
    {
        if (is_array($driver)) {
            $driver = $this->createDriverFromParameters($driver);
        }

        if (!$driver instanceof Adapter\Driver) {
            throw new \InvalidArgumentException('Invalid driver');
        }

        $driver->checkEnvironment();
        $this->setDriver($driver);

        if ($platform == null) {
            $platform = $this->createPlatformFromDriver($driver);
        }

        $this->setPlatform($platform);

        $this->queryResultSetPrototype = ($queryResultPrototype) ?: new ResultSet\ResultSet();
    }

    /**
     * setDriver()
     * 
     * @param Adapter\Driver $driver
     * @return Adapter
     */
    public function setDriver(Adapter\Driver $driver)
    {
        $this->driver = $driver;
        return $this;
    }

    /**
     * @param array $parameters
     * @return Adapter\Driver
     * @throws \InvalidArgumentException
     */
    public function createDriverFromParameters(array $parameters)
    {
        if (!isset($parameters['type']) || !is_string($parameters['type'])) {
            throw new \InvalidArgumentException('createDriverFromParameters() expects a "type" key to be present inside the parameters');
        }

        $className = $parameters['type'];
        if (strpos($className, '\\') === false) {
            $className = self::BUILTIN_DRIVERS_NAMESPACE . '\\' . $parameters['type'];
        }
        unset($parameters['type']);
        $driver = $className;

        if (is_string($driver) && class_exists($driver, true)) {
            $driver = new $driver($parameters);
        } else {
            throw new \InvalidArgumentException('Class by name ' . $driver . ' not found', null, null);
        }

        if (!$driver instanceof Adapter\Driver) {
            throw new \InvalidArgumentException('$driver provided is neither a driver class name or object of type DriverInterface', null, null);
        }

        return $driver;
    }
    
    /**
     * getDriver()
     * 
     * @throws Exception
     * @return Adapter\Driver
     */
    public function getDriver()
    {
        if ($this->driver == null) {
            throw new \Exception('Driver has not been set or configured for this adapter.');
        }
        return $this->driver;
    }

    /**
     * @param string $queryMode
     * @return Adapter
     * @throws \InvalidArgumentException
     */
    public function setQueryMode($queryMode)
    {
        if (!in_array($queryMode, array(self::QUERY_MODE_EXECUTE, self::QUERY_MODE_PREPARE))) {
            throw new \InvalidArgumentException('mode must be one of query_execute or query_prepare');
        }
        
        $this->queryMode = $queryMode;
        return $this;
    }

    /**
     * @param Adapter\Platform $platform
     * @return Adapter
     */
    public function setPlatform(Adapter\Platform $platform)
    {
        $this->platform = $platform;
        return $this;
    }

    /**
     * @return Adapter\Platform
     */
    public function getPlatform()
    {
        return $this->platform;
    }

    /**
     * @param Adapter\Driver $driver
     * @return Adapter\Platform
     */
    public function createPlatformFromDriver(Adapter\Driver $driver)
    {
        // consult driver for platform implementation
        $platform = $driver->getDatabasePlatformName(Adapter\Driver::NAME_FORMAT_CAMELCASE);
        if ($platform == '') {
            $platform = 'Sql92';
        }
        if ($platform{0} != '\\') {
            $platform = self::BUILTIN_PLATFORMS_NAMESPACE . '\\' . $platform;
        }
        return new $platform;
    }

    /**
     * query() is a convenience function
     *
     * @param string $sql
     * @param string|array $parametersOrPrepareExecuteFlag
     * @return Zend\Db\Adapter\DriverStatement|
     */
    public function query($sql, $parametersOrPrepareExecuteFlag = self::QUERY_MODE_PREPARE)
    {
        if (is_string($parametersOrPrepareExecuteFlag) && in_array($parametersOrPrepareExecuteFlag, array(self::QUERY_MODE_PREPARE, self::QUERY_MODE_EXECUTE))) {
            $mode = $parametersOrPrepareExecuteFlag;
        } elseif (is_array($parametersOrPrepareExecuteFlag)) {
            $mode = self::QUERY_MODE_PREPARE;
            $parameters = $parametersOrPrepareExecuteFlag;
        } else {
            throw new \Exception('Parameter 2 to this method must be a flag or an array');
        }

        $c = $this->driver->getConnection();

        if ($mode == self::QUERY_MODE_PREPARE) {
            $statement = $c->prepare($sql);
            return $statement;
            // @todo determine if we fulfill the request
            // $result = $statement->execute($parameters);
        } else {
            $result = $c->execute($sql);
        }

        $resultSetProducing = (stripos(trim($sql), 'SELECT') === 0); // will this sql produce a rowset?

        if ($resultSetProducing) {
            $resultSet = clone $this->queryResultSetPrototype;
            $resultSet->setDataSource($result);
            return $resultSet;
        }

        return $result;
    }

    /**
     * @param $name
     */
    public function __get($name)
    {
        switch (strtolower($name)) {
            case 'driver': return $this->driver;
            case 'platform': return $this->platform;
        }
        throw new \Exception('Invalid magic property on adapter');
    }

}
