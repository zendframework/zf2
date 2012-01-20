<?php
/**
 * File DocBlock
 */

namespace Zend\Db;

/**
 * Class DocBlock
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
    
    const BUILTIN_DRIVERS_NAMESPACE = 'Zend\Db\Adapter\Driver';
    const BUILTIN_PLATFORMS_NAMESPACE = 'Zend\Db\Adapter\Platform';

    /**
     * @var \Zend\Db\Adapter\Driver
     */
    protected $driver = null;

    /**
     * @var Zend\Db\Adapter\Platform
     */
    protected $platform = null;

    protected $queryResultPrototype = null;

    protected $queryMode = self::QUERY_MODE_PREPARE;


    /**
     * @param $driver
     * @param null $platform
     * @param null|ResultSet\ResultSet $resultPrototype
     */
    public function __construct($driver, Adapter\Platform $platform = null, ResultSet\ResultSet $queryResultPrototype = null)
    {
        if (is_array($driver)) {
            $driver = $this->createDriverFromParameters($driver);
        }

        if (!$driver instanceof Adapter\Driver) {
            throw new \InvalidArgumentException('Invalid driver');
        }

        $this->setDriver($driver);

        if ($platform == null) {
            $platform = $this->createPlatformFromDriver($driver);
        }

        $this->setPlatform($platform);

        $this->queryResultPrototype = ($queryResultPrototype) ?: new ResultSet\ResultSet();
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

    public function createDriverFromParameters(array $parameters)
    {
        $driverParameters = $parameters;
        if (isset($driverParameters['type']) && is_string($driverParameters['type'])) {
            $className = $driverParameters['type'];
            if (strpos($className, '\\') === false) {
                $className = self::BUILTIN_DRIVERS_NAMESPACE . '\\' . $driverParameters['type'];
            }
            unset($driverParameters['type']);
        }
        $driver = $className;

        if (is_string($driver) && class_exists($driver, true)) {
            $driver = new $driver($driverParameters);
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
     * @return \Zend\Db\Adapter\Driver
     */
    public function getDriver()
    {
        if ($this->driver == null) {
            throw new \Exception('Driver has not been set or configured for this adapter.');
        }
        return $this->driver;
    }
    
    public function setQueryMode($queryMode)
    {
        if (!in_array($queryMode, array(self::QUERY_MODE_EXECUTE, self::QUERY_MODE_PREPARE))) {
            throw new \InvalidArgumentException('mode must be one of query_execute or query_prepare');
        }
        
        $this->queryMode = $queryMode;
        return $this;
    }

    public function setPlatform($platform)
    {
        if (is_string($platform)) {
            if ($platform{0} != '\\') {
                $platform = self::DEFAULT_PLATFORM_NAMESPACE . '\\' . $platform;
            }
            if (!class_exists($platform, true)) {
                throw new \InvalidArgumentException('Class by name ' . $platform . ' not found', null, null);
            }
            $platform = new $platform;
        }
        
        if (!$platform instanceof Adapter\Platform) {
            throw new \InvalidArgumentException('Platform must be of type Zend\Db\Adapter\Platform');
        }
        
        $this->platform = $platform;
        return $this;
    }

    /**
     * @var \Zend\Db\Adapter\Platform
     */
    public function getPlatform()
    {
        return $this->platform;
    }
    
    public function createPlatformFromDriver(Adapter\Driver $driver)
    {
        // consult driver for platform implementation
        $platform = $this->getDriver()->getDatabasePlatformName(Adapter\Driver::NAME_FORMAT_CAMELCASE);
        if ($platform == '') {
            $platform = 'Sql92';
        }
        if ($platform{0} != '\\') {
            $platform = self::DEFAULT_PLATFORM_NAMESPACE . '\\' . $platform;
        }
        return new $platform;
    }
    
    /**
     * query() is a convienince function
     * 
     * @return Zend\Db\Adapter\DriverStatement
     */
    public function query($sql, $prepareOrExecute = self::QUERY_MODE_PREPARE)
    {
        $c = $this->getDriver()->getConnection();
        return (($prepareOrExecute == self::QUERY_MODE_EXECUTE) ? $c->execute($sql) : $c->prepare($sql));
    }

}
