<?php

namespace Zend\Db;

class Adapter
{
    const QUERY_MODE_EXECUTE = 'execute';
    const QUERY_MODE_PREPARE = 'prepare';

    const PREPARE_TYPE_POSITIONAL = 'positional';
    const PREPARE_TYPE_NAMED = 'named';
    
    const DEFAULT_DRIVER_NAMESPACE = 'Zend\Db\Adapter\Driver';
    const DEFAULT_PLATFORM_NAMESPACE = 'Zend\Db\Adapter\Platform';

    /**
     * @var \Zend\Db\Adapter\Driver
     */
    protected $driver = null;

    /**
     * @var Zend\Db\Adapter\Platform
     */
    protected $platform = null;

    protected $queryMode = self::QUERY_MODE_PREPARE;
    
    protected $preferredPrepareType = null;
    
    protected $queryReturnClass = 'Zend\Db\ResultSet\ResultSet';

    
    public function __construct($options = array())
    {
        if ($options) {
            $this->setOptions($options);
        }
    }
    
    /**
     * setOptions()
     * 
     * @param array $options
     */
    public function setOptions(array $options)
    {
        foreach ($options as $optionName => $optionValue) {
            if (method_exists($this, 'set' . $optionName)) {
                $this->{'set' . $optionName}($optionValue);
            }
        }
    }

    /**
     * setDriver()
     * 
     * @param array|\Zend\Db\Adapter\Driver\AbstractDriver $driver
     */
    public function setDriver($driver)
    {
        if (is_array($driver)) {
            $driverOptions = $driver;
            if (isset($driverOptions['type']) && is_string($driverOptions['type'])) {
                $className = $driverOptions['type'];
                if ($driver['type']{0} != '\\') {
                    $className = self::DEFAULT_DRIVER_NAMESPACE . '\\' . $driverOptions['type'];
                }
                unset($driverOptions['type']);
            }
            $driver = $className;
        }
            
        if (is_string($driver) && class_exists($driver, true)) {
            $driver = new $driver;
        } else {
            throw new \InvalidArgumentException('Class by name ' . $driver . ' not found', null, null);
        }
        
        if (!$driver instanceof Adapter\Driver) {
            throw new \InvalidArgumentException('$driver provided is neither a driver class name or object of type DriverInterface', null, null);
        }
        
        if (isset($driverOptions)) {
            $driver->setOptions($driverOptions);
        }
        
        $this->driver = $driver;
        return $this;
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
        if (!isset($this->platform)) {
            $this->lazyInitializePlatform();
        }
        return $this->platform;
    }
    
    protected function lazyInitializePlatform()
    {
        // consult driver for platform implementation
        $platform = $this->getDriver()->getDatabasePlatformName(Adapter\Driver::NAME_FORMAT_CAMELCASE);
        if ($platform == '') {
            $platform = 'Sql92';
        }
        if ($platform{0} != '\\') {
            $platform = self::DEFAULT_PLATFORM_NAMESPACE . '\\' . $platform;
        }
        $this->setPlatform(new $platform);
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
