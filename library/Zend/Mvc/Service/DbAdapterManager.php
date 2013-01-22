<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Db
 */

namespace Zend\Mvc\Service;


use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Platform\Platform;
use Zend\Mvc\Service\Exception\DbAdapterManagerAdapterAllreadyRegistered;
use Zend\Mvc\Service\Exception\DbAdapterManagerAdapterCoundInit;
use Zend\Mvc\Service\Exception\DbAdapterManagerAdapterNotExist;
use Zend\Mvc\Service\Exception\DbAdapterManagerAdapterConfigNotVaild;
use Exception;

class DbAdapterManager implements ServiceLocatorAwareInterface
{
    /**
     *
     * @var Adapter[]
     */
    protected $_dbAdapter = array();

    protected $_dbAdapterConfig = array();

    /**
     * @var ServiceLocatorInterface
     */
    protected $_serviceLocator;

    /**
     * @param array $config
     * @throws DbAdapterManagerAdapterAllreadyRegistered
     */
    public function addAdapterConfig(array $configArray)
    {
        foreach ($configArray as $key => $config) {
            if ( $this->hasAdapter($key) ) {
                throw new DbAdapterManagerAdapterAllreadyRegistered(sprintf("adapter with key(%s) is allready registered",$key));
            } elseif ( $this->hasAdapterConfig($key) ) {
                throw new DbAdapterManagerAdapterAllreadyRegistered(sprintf("adapter config with key(%s) is allready defined",$key));
            }

            $this->_dbAdapterConfig[ $key ] = $config;
        }
    }

    /**
     * @param ServiceLocatorInterface $serviceLocator
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->_serviceLocator = $serviceLocator;
    }

    /**
     * @return ServiceLocatorInterface
     */
    public function getServiceLocator()
    {
        return $this->_serviceLocator;
    }

    /**
     * @param array $config
     * @throws DbAdapterManagerAdapterNotExist
     */
    public function getAdapterConfig($adapterKey)
    {
        if ( !$this->hasAdapterConfig($adapterKey) ) {
            throw new DbAdapterManagerAdapterNotExist(sprintf("adapter config with the key (%s) not exist",$adapterKey));
        }
        return $this->_dbAdapterConfig[ $adapterKey ];
    }

    /**
     * @param array $config
     * @return bool
     */
    public function hasAdapterConfig($adapterKey)
    {
        return ( isset($this->_dbAdapterConfig[ $adapterKey ]) );
    }

    /**
     * @param string $key
     * @param Adapter $adapter
     * @throws DbAdapterManagerAdapterAllreadyRegistered
     */
    public function addAdapter($key, Adapter $adapter)
    {
        if ( $this->hasAdapter($key) ) {
            if ( $this->_dbAdapter[$key] === $adapter ) {
                return true;
            }
            throw new DbAdapterManagerAdapterAllreadyRegistered(sprintf("adapter key (%s) allready exist",$key));
        }

        $this->_dbAdapter[ $key ] = $adapter;
    }

    /**
     *
     * @param string $key
     * @return bool
     */
    public function hasAdapter($key)
    {
        return ( isset($this->_dbAdapter[$key]) );
    }

    /**
     * @param string $key
     * @throws DbAdapterManagerAdapterNotExist || DbAdapterManagerAdapterCoundInit
     * @return Adapter
     */
    public function getAdapter($key)
    {
        if ( !$this->hasAdapter($key) ) {
            if ( !$this->hasAdapterConfig($key) ) {
                throw new DbAdapterManagerAdapterNotExist(sprintf("adapter key (%s) not exist",$key));
            }

            try {
                $this->initAdapter($key);
            } catch (\Exception $exception) {
                throw new DbAdapterManagerAdapterCoundInit(sprintf("adapter cound init for key (%s)",$key),0,$exception);
            }

            if ( !$this->hasAdapter($key) ) {
                throw new DbAdapterManagerAdapterCoundInit(sprintf("adapter cound init for key (%s)",$key));
            }
        }

        return $this->_dbAdapter[ $key ];
    }

    /**
     * @param string $key
     * @throws DbAdapterManagerAdapterConfigNotVaild
     * @return Adapter
     */
    protected function initAdapter($key)
    {
        $config = $this->_dbAdapterConfig[ $key ];

        if ( is_string($config) ) {
            $this->_dbAdapter[ $key ] = $this->getAdapter($config);
        } elseif (!is_array($config) ||
                  !array_key_exists('driver', $config)
        ) {
            throw new DbAdapterManagerAdapterConfigNotVaild(sprintf("adapter config on key (%s) is not an valid key or array", $key));
        } else {
            try {
                $this->_dbAdapter[ $key ] = $this->adapterFactory( $config, $this->getServiceLocator() );
            } catch (Exception $exception) {
                if ( $exception instanceof DbAdapterManagerAdapterCoundInit ) {
                    $previous = $exception->getPrevious();
                } else {
                    $previous = $exception;
                }
                throw new DbAdapterManagerAdapterCoundInit(sprintf("adapter cound init for key", $key),0,$previous );
            }
        }

        return $this->_dbAdapter[ $key ];
    }

    /**
     * return a platform object from a config
     *
     * @param array $config
     * @param ServiceLocatorInterface $serviceLocator
     * @return Platform || null
     */
    protected function getPlatformObjectFromConfig ($config, ServiceLocatorInterface $serviceLocator=null)
    {
        if ( !isset($config['platform']) ) {
            goto RETURN_NULL;
        }

        if ( is_string ($config['platform']) ) {
            if( class_exists($config['platform']) ) {
                $platform = new $config['platform']();
            } else {
                if ( $serviceLocator === null ) {
                    $serviceLocator = $this->getServiceLocator();
                }
                $platform = $serviceLocator->get($config['platform']);
            }
        } else {
            $platform = $config['platform'];
        }

        if ( is_object($platform) ) {
            goto RETURN_OBJECT;
        }

        RETURN_NULL:
            return null;

        RETURN_OBJECT:
            return $platform;
    }

    /**
     * return a driver object from a config
     *
     * @param array $config
     * @param ServiceLocatorInterface $serviceLocator
     * @return Platform || null
     */
    protected function getDriverObjectFromConfig ($config, ServiceLocatorInterface $serviceLocator=null)
    {
        if ( !isset($config['driver']) ) {
            // @todo: throw a error or return null ?
            goto RETURN_NULL;
        }

        if ( is_array($config['driver']) || is_object($config['driver']) ) {
            $driver = $config['driver'];
        } elseif ( is_string($config['driver']) ) {
            if( class_exists($config['driver']) ) {
                $driver = new $config['driver']();
            } else {
                $driver = $serviceLocator->get($config['driver']);
            }

            if ( !is_object($driver) ) {
                throw new DbAdapterManagerAdapterConfigNotVaild("database config['driver'] string is not a confirmed class/service name");
            }
        } else {
            throw new DbAdapterManagerAdapterConfigNotVaild("database config['driver'] must be a array or string of class/service name");
        }

        goto RETURN_OBJECT;

        RETURN_NULL:
            return null;

        RETURN_OBJECT:
            return $driver;
    }

    /**
     * @param array $config
     * @param ServiceLocatorInterface $serviceLocator
     * @throws DbAdapterManagerAdapterConfigNotVaild
     * @return Adapter
     */
    public function adapterFactory($config, ServiceLocatorInterface $serviceLocator=null)
    {
        if ( $serviceLocator === null ) {
            $serviceLocator = $this->getServiceLocator();
        }

        $driver               = null;
        $platform             = null;
        $queryResultPrototype = null;

        $driver = $this->getDriverObjectFromConfig($config,$serviceLocator);
        $platform = $this->getPlatformObjectFromConfig($config,$serviceLocator);

        if ( isset($config['queryResultPrototype']) ) {
            if( class_exists($config['queryResultPrototype']) ) {
                $queryResultPrototype = new $config['queryResultPrototype']();
            } else {
                $queryResultPrototype = $serviceLocator->get($config['queryResultPrototype']);
            }
        }

        if ( !is_object($queryResultPrototype) ) {
            $queryResultPrototype = null;
        }

        try {
            $adapter = new Adapter($driver,
                           $platform,
                           $queryResultPrototype
                          );
        } catch (\Exception $exception) {
            throw new DbAdapterManagerAdapterCoundInit("adapter cound init",0,$exception);
        }
        
        return $adapter;
    }
}
